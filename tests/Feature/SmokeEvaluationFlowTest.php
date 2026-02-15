<?php

namespace Tests\Feature;

use App\IdentitasResponden;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class SmokeEvaluationFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_end_to_end_flow_from_responden_input_to_dashboard(): void
    {
        $registerResponse = $this->post(route('register'), [
            'name' => 'Smoke User',
            'email' => 'smoke@example.test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $registerResponse->assertRedirect(route('verification.notice'));

        $user = User::where('email', 'smoke@example.test')->firstOrFail();
        $this->assertAuthenticatedAs($user);

        $this->post(route('responden.store'), [
            'identitas_instansi_pemerintah' => 'Dinas Contoh',
            'email' => 'responden@example.test',
            'nik' => '1234567890123456',
            'nip' => '198001012010011001',
            'nomor_hp' => '08123456789',
            'jabatan' => 'Pengelola TIK',
            'alamat' => 'Jalan Contoh',
        ])->assertRedirect(route('responden.index'));

        $responden = IdentitasResponden::where('user_id', $user->id)->firstOrFail();

        $now = now();

        $insertParameter = function (array $data) use ($now): int {
            return DB::table('parameters')->insertGetId(array_merge($data, [
                'created_at' => $now,
                'updated_at' => $now,
            ]));
        };

        $kategoriSeParameterIds = [];
        for ($i = 1; $i <= 10; $i++) {
            $kategoriSeParameterIds[] = $insertParameter([
                'bagian' => 'I',
                'tahap' => null,
                'kategori_kontrol' => null,
                'parameter' => 'Parameter Kategori SE #' . $i,
                'skor' => null,
            ]);
        }

        $tataKelolaId = $insertParameter([
            'bagian' => 'II',
            'tahap' => 'ii',
            'kategori_kontrol' => '1',
            'parameter' => 'Parameter Tata Kelola',
            'skor' => null,
        ]);

        $risikoId = $insertParameter([
            'bagian' => 'III',
            'tahap' => 'ii',
            'kategori_kontrol' => '1',
            'parameter' => 'Parameter Risiko',
            'skor' => null,
        ]);

        $kerangkaKerjaId = $insertParameter([
            'bagian' => 'IV',
            'tahap' => 'ii',
            'kategori_kontrol' => '1',
            'parameter' => 'Parameter Kerangka Kerja',
            'skor' => null,
        ]);

        $pengelolaanAsetId = $insertParameter([
            'bagian' => 'V',
            'tahap' => 'ii',
            'kategori_kontrol' => '1',
            'parameter' => 'Parameter Pengelolaan Aset',
            'skor' => null,
        ]);

        $teknologiId = $insertParameter([
            'bagian' => 'VI',
            'tahap' => 'ii',
            'kategori_kontrol' => '1',
            'parameter' => 'Parameter Teknologi',
            'skor' => null,
        ]);

        $this->put(route('kategori-se.update', ['kategori_se' => $responden->id]), [
            'parameter_id' => $kategoriSeParameterIds,
            'skor' => array_fill(0, 10, 'A'),
        ])->assertRedirect(route('kategori-se.index'));

        $domainRequest = [
            'identitas_responden_id' => $responden->id,
            'skor' => [3],
        ];

        $this->put(route('tata-kelola.update', ['tata_kelola' => $responden->id]), array_merge($domainRequest, [
            'parameter_id' => [$tataKelolaId],
        ]))->assertRedirect(route('tata-kelola.index'));

        $this->put(route('risiko.update', ['risiko' => $responden->id]), array_merge($domainRequest, [
            'parameter_id' => [$risikoId],
        ]))->assertRedirect(route('risiko.index'));

        $this->put(route('kerangka-kerja.update', ['kerangka_kerja' => $responden->id]), array_merge($domainRequest, [
            'parameter_id' => [$kerangkaKerjaId],
        ]))->assertRedirect(route('kerangka-kerja.index'));

        $this->put(route('pengelolaan-aset.update', ['pengelolaan_aset' => $responden->id]), array_merge($domainRequest, [
            'parameter_id' => [$pengelolaanAsetId],
        ]))->assertRedirect(route('pengelolaan-aset.index'));

        $this->put(route('teknologi.update', ['teknologi' => $responden->id]), array_merge($domainRequest, [
            'parameter_id' => [$teknologiId],
        ]))->assertRedirect(route('teknologi.index'));

        $dashboardResponse = $this->get(route('home'));
        $dashboardResponse->assertOk();
        $dashboardResponse->assertSee('Total Skor');

        $this->assertDatabaseCount('kategori_s_es', 10);
        $this->assertDatabaseCount('tata_kelolas', 1);
        $this->assertDatabaseCount('risikos', 1);
        $this->assertDatabaseCount('kerangka_kerjas', 1);
        $this->assertDatabaseCount('pengelolaan_asets', 1);
        $this->assertDatabaseCount('teknologis', 1);
    }
}
