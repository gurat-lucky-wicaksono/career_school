<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Job;
use App\Models\Major;
use App\Models\Role;
use App\Models\SchoolClass;
use App\Models\Skill;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminMasterCrudTest extends TestCase
{
    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::firstOrCreate(['nama' => 'admin'], ['label' => 'Administrator']);
        $this->admin = User::where('email', 'admin@demo.com')->first() ?? User::create([
            'role_id' => $role->id,
            'name' => 'Administrator BKK SMKN 1',
            'username' => 'admin_bkk',
            'email' => 'admin@demo.com',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
    }

    public function test_teacher_crud_lifecycle(): void
    {
        // 1. Create Teacher
        $uniqueNip = 'NIP' . rand(10000000, 99999999);
        $createResponse = $this->actingAs($this->admin)->post(route('admin.teachers.store'), [
            'name' => 'Guru Penguji CRUD',
            'email' => 'guru_crud_' . rand(1000, 9999) . '@smkn1.sch.id',
            'nip' => $uniqueNip,
            'mata_pelajaran' => 'Pemrograman Berorientasi Objek',
            'is_wali_kelas' => 1,
            'password' => 'secret123',
        ]);

        $createResponse->assertRedirect(route('admin.teachers.index'));
        $this->assertDatabaseHas('teachers', ['nip' => $uniqueNip]);

        $teacher = Teacher::where('nip', $uniqueNip)->first();
        $this->assertNotNull($teacher);

        // 2. Search & Filter Teacher Index
        $searchResponse = $this->actingAs($this->admin)->get(route('admin.teachers.index', ['search' => $uniqueNip]));
        $searchResponse->assertStatus(200);
        $searchResponse->assertSee($uniqueNip);

        // 3. Update Teacher
        $updateResponse = $this->actingAs($this->admin)->put(route('admin.teachers.update', $teacher->id), [
            'name' => 'Guru Penguji Diperbarui',
            'email' => $teacher->user->email,
            'nip' => $uniqueNip,
            'mata_pelajaran' => 'Kecerdasan Buatan',
            'is_wali_kelas' => 0,
        ]);

        $updateResponse->assertRedirect(route('admin.teachers.index'));
        $this->assertDatabaseHas('teachers', ['id' => $teacher->id, 'mata_pelajaran' => 'Kecerdasan Buatan', 'is_wali_kelas' => 0]);

        // 4. Delete Teacher
        $deleteResponse = $this->actingAs($this->admin)->delete(route('admin.teachers.destroy', $teacher->id));
        $deleteResponse->assertRedirect(route('admin.teachers.index'));
        $this->assertDatabaseMissing('teachers', ['id' => $teacher->id]);
    }

    public function test_major_and_class_crud_lifecycle(): void
    {
        // 1. Create Major
        $kodeMajor = 'TK' . rand(10, 99);
        $createMajor = $this->actingAs($this->admin)->post(route('admin.majors.store'), [
            'kode' => $kodeMajor,
            'nama' => 'Teknik Komputer & Jaringan Test',
            'kepala_jurusan' => 'Bapak Kepala Jurusan',
            'keterangan' => 'Kurikulum vokasi terpadu',
        ]);
        $createMajor->assertRedirect(route('admin.majors.index'));
        $this->assertDatabaseHas('majors', ['kode' => $kodeMajor]);
        $major = Major::where('kode', $kodeMajor)->first();

        // 2. Create Class
        $className = 'X TKJ ' . rand(1, 9);
        $createClass = $this->actingAs($this->admin)->post(route('admin.classes.store'), [
            'nama' => $className,
            'major_id' => $major->id,
            'tingkat' => 10,
            'tahun_ajaran' => '2025/2026',
        ]);
        $createClass->assertRedirect(route('admin.classes.index'));
        $this->assertDatabaseHas('classes', ['nama' => $className]);
        $class = SchoolClass::where('nama', $className)->first();

        // 3. Delete Class & Major
        $deleteClass = $this->actingAs($this->admin)->delete(route('admin.classes.destroy', $class->id));
        $deleteClass->assertRedirect(route('admin.classes.index'));
        $this->assertDatabaseMissing('classes', ['id' => $class->id]);

        $deleteMajor = $this->actingAs($this->admin)->delete(route('admin.majors.destroy', $major->id));
        $deleteMajor->assertRedirect(route('admin.majors.index'));
        $this->assertDatabaseMissing('majors', ['id' => $major->id]);
    }

    public function test_skill_crud_lifecycle(): void
    {
        $skillName = 'Skill Test ' . rand(1000, 9999);
        $create = $this->actingAs($this->admin)->post(route('admin.skills.store'), [
            'nama' => $skillName,
            'kategori' => 'Pengembangan Web',
        ]);
        $create->assertRedirect(route('admin.skills.index'));
        $this->assertDatabaseHas('skills', ['nama' => $skillName]);

        $skill = Skill::where('nama', $skillName)->first();

        $update = $this->actingAs($this->admin)->put(route('admin.skills.update', $skill->id), [
            'nama' => $skillName . ' Updated',
            'kategori' => 'Mobile Apps',
        ]);
        $update->assertRedirect(route('admin.skills.index'));
        $this->assertDatabaseHas('skills', ['id' => $skill->id, 'kategori' => 'Mobile Apps']);

        $delete = $this->actingAs($this->admin)->delete(route('admin.skills.destroy', $skill->id));
        $delete->assertRedirect(route('admin.skills.index'));
        $this->assertDatabaseMissing('skills', ['id' => $skill->id]);
    }
}
