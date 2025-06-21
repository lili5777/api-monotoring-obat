<?php

namespace App\Http\Controllers;

// use App\Libraries\ARIMA\Arima;

use App\Models\ARIMA;
use App\Models\Obat;
use App\Models\Rak;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Carbon\Carbon;
use Dotenv\Validator;
use Illuminate\Support\Facades\Response;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator as FacadesValidator;

class AuthController extends Controller
{
    //
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('username', $request->username)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        return response()->json([
            'message' => 'Login successful',
            'user' => [
                'id' => $user->id,
                'username' => $user->username,
                'role' => $user->role,
            ],
        ]);
    }

    public function register(Request $request)
    {
        // Validasi input
        $validator = FacadesValidator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'username' => 'required|string|max:50|unique:users',
            'password' => 'required|string|min:6',
        ]);

        // Jika validasi gagal
        if ($validator->fails()) {
            $errors = $validator->errors();

            // Cek error untuk masing-masing field
            if ($errors->has('email')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Registration failed',
                    'error' => 'Email already exists'
                ], 400);
            }

            if ($errors->has('username')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Registration failed',
                    'error' => 'Username already exists'
                ], 400);
            }

            return response()->json([
                'success' => false,
                'message' => 'Registration failed',
                'errors' => $errors->all()
            ], 400);
        }

        // Buat user baru
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'role' => 'user', // Default role
            ]);

            // Response sukses
            return response()->json([
                'success' => true,
                'message' => 'Registration successful',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'username' => $user->username,
                    'role' => $user->role,
                ]
            ], 201);
        } catch (\Exception $e) {
            // Handle error lainnya
            return response()->json([
                'success' => false,
                'message' => 'Registration failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function Rak()
    {
        $rak = Rak::all();
        return response()->json([
            'message' => 'data rak berhasil',
            'status' => true,
            'data' => $rak,
        ]);
    }

    public function Obat()
    {
        $obat = Obat::all();
        $trans = Transaksi::all();
        $jum = 0;
        foreach ($trans as $tran) {
            $jum += $tran->jumlah;
        }

        return response()->json([
            'message' => 'Data obat berhasil diambil',
            'status' => true,
            'obat' => $obat,
            'total' => $obat->count(),
            'jum' => $jum,
        ]);
    }

    public function storerak(Request $request)
    {
        $request->validate([
            'nama_rak' => 'required|string|max:255',
            'kapasitas' => 'required',
        ]);

        $rak = new Rak();
        $rak->nama_rak = $request->nama_rak;
        $rak->kapasitas = $request->kapasitas;
        $rak->kosong = $request->kapasitas;
        $rak->terisi = 0;
        $rak->save();

        return response()->json([
            'success' => true,
            'message' => 'Rak berhasil ditambahkan.',
            'data' =>
            [
                'id' => $rak->id,
                'nama_rak' => $rak->nama_rak,
                'kapasitas' => $rak->kapasitas,
                'kosong' => $rak->kosong,
                'terisi' => $rak->terisi,
            ],
        ], 201);
    }

    public function deleterak($id)
    {
        try {
            $rak = Rak::findOrFail($id);
            $rak->delete();
            return response()->json([
                'success' => true,
                'message' => 'Obat berhasil dihapus.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus obat.',
            ], 500);
        }
    }

    public function updaterak(Request $request, $id)
    {
        $request->validate([
            'nama_rak' => 'required|string|max:255',
            'kapasitas' => 'required',
        ]);

        $rak = Rak::find($id);

        if (!$rak) {
            return response()->json(['error' => 'Obat tidak ditemukan'], 404);
        }
        if($request->kapasitas < $rak->terisi){
            return response()->json([
                'success' => false,
                'message' => 'Kapasitas tidak boleh lebih kecil dari obat yang terisi',
            ], 404) ;
        }elseif($request->kapasitas > $rak->kapasitas){
            $sisa = $request->kapasitas - $rak->kapasitas;
            $rak->kosong += $sisa;
        }else{
            $sisa =  $rak->kapasitas - $request->kapasitas;
            $rak->kosong -= $sisa;
        }

        $rak->kapasitas=$request->kapasitas;
        $rak->save();

        return response()->json([
            'success' => true,
            'message' => 'Obat berhasil diperbarui.',
            'data' =>
            [
                'id' => $rak->id,
                'nama_rak' => $rak->nama_rak,
                'kapasitas' => $rak->kapasitas,
                'kosong' => $rak->kosong,
                'terisi' => $rak->terisi,
            ],
        ]);
    }

    public function storeobat(Request $request)
    {
        $validated = $request->validate([
            'nama_obat' => 'required|string|max:255',
            'kode' => 'required',
        ]);

        $obat = Obat::create($validated);
        $trans = Transaksi::all();
        $jum = 0;
        foreach ($trans as $tran) {
            $jum += $tran->jumlah;
        }

        return response()->json([
            'success' => true,
            'message' => 'Obat berhasil ditambahkan.',
            'data' =>
            [
                'id' => $obat->id,
                'nama_obat' => $obat->nama_obat,
                'kode' => $obat->kode,
            ],
            'total' => $obat->count(),
            'jum' => $jum,
        ], 201);
    }

    public function updateObat(Request $request, $id)
    {
        $validated = $request->validate([
            'nama_obat' => 'required|string|max:255',
            'kode' => 'required',
        ]);

        $obat = Obat::find($id);

        if (!$obat) {
            return response()->json(['error' => 'Obat tidak ditemukan'], 404);
        }

        $obat->update($validated);



        return response()->json([
            'success' => true,
            'message' => 'Obat berhasil diperbarui.',
            'data' =>
            [
                'id' => $obat->id,
                'nama_obat' => $obat->nama_obat,
                'kode' => $obat->kode,
            ],
        ]);
    }

    public function deleteObat($id)
    {
        try {
            $obat = Obat::findOrFail($id);
            $obat->delete();
            $trans = Transaksi::all();
            $jum = 0;
            foreach ($trans as $tran) {
                $jum += $tran->jumlah;
            }

            return response()->json([
                'success' => true,
                'message' => 'Obat berhasil dihapus.',
                'total' => $obat->count(),
                'jum' => $jum,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus obat.',
            ], 500);
        }
    }

    // public function storetransa(Request $request)
    // {
    //     $validated = $request->validate([
    //         'nama_obat' => 'required|string|max:255',
    //         'jumlah' => 'required|integer|min:1',
    //         'kadaluarsa' => 'required|date',
    //         'id_rak' => 'required|exists:raks,id',
    //     ]);

    //     $obat = Obat::create($validated);

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Obat berhasil ditambahkan.',
    //         'data' =>
    //         [
    //             'id' => $obat->id,
    //             'nama' => $obat->nama_obat,
    //             'jumlah' => $obat->jumlah,
    //             'kadaluarsa' => $obat->kadaluarsa,
    //             'rak' => $obat->rak->nama_rak ?? 'Rak tidak ditemukan',
    //         ],
    //     ], 201);
    // }
    // public function updateTran(Request $request, $id)
    // {
    //     $validated = $request->validate([
    //         'nama_obat' => 'required|string|max:255',
    //         'jumlah' => 'required|integer|min:1',
    //         'kadaluarsa' => 'required|date',
    //         'id_rak' => 'required|exists:raks,id',
    //     ]);

    //     $obat = Obat::find($id);

    //     if (!$obat) {
    //         return response()->json(['error' => 'Obat tidak ditemukan'], 404);
    //     }

    //     $obat->update($validated);

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Obat berhasil diperbarui.',
    //         'data' => [
    //             'id' => $obat->id,
    //             'nama' => $obat->nama_obat,
    //             'jumlah' => $obat->jumlah,
    //             'kadaluarsa' => $obat->kadaluarsa,
    //             'rak' => $obat->rak->nama_rak ?? 'Rak tidak ditemukan',
    //         ],
    //     ]);
    // }

    public function Transaksi($id)
    {
        $transaksi = Transaksi::with('rak', 'obat')->where('id_obat', $id)->get();
        if ($transaksi->isEmpty()) {
            return response()->json([
                'message' => 'Data transaksi tidak ditemukan',
                'transaksi' => [] // Ubah key dari 'obat' menjadi 'transaksi'
            ], 404); // Not Found
        }

        return response()->json([
            'message' => 'Data transaksi berhasil diambil',
            'transaksi' => $transaksi->map(function ($item) {
                return [
                    'id' => $item->id,
                    'jumlah' => $item->jumlah,
                    'kadaluarsa' => $item->kadaluarsa,
                    'rak' => $item->rak->nama_rak ?? 'Rak tidak ditemukan',
                    'obat' => $item->obat->nama_obat ?? 'Obat tidak ditemukan',
                    'masuk' => $item->masuk,
                ];
            }),
        ]);
    }

    public function tambahtransaksi(Request $request)
    {
        try {
            $validated = $request->validate([
                'idrak' => 'required',
                'idobat' => 'required',
                'jumlah' => 'required|integer',
                'masuk' => 'required|date',
                'kadaluarsa' => 'required|date',
            ]);

            $rakk=Rak::where('id', $request->idrak)->first();
            if (!$rakk) {
                return response()->json([
                    'success' => false,
                    'message' => 'Rak tidak ditemukan',
                ], 404);
            }
            if ($request->jumlah > $rakk->kosong) {
                return response()->json([
                    'success' => false,
                    'message' => "Kapasitas rak hanya dapat menampung {$rakk->kosong} obat",
                ], 404);
            }

            $transaksi = new Transaksi();
            $transaksi->id_obat = $request->idobat;
            $transaksi->jumlah = $request->jumlah;
            $transaksi->kadaluarsa = $request->kadaluarsa;
            $transaksi->id_rak = $request->idrak;
            $transaksi->masuk = $request->masuk;
            // dd($transaksi);
            $transaksi->save();
            $transaksiArray = $transaksi->toArray();

            $rakk->terisi += $request->jumlah;
            $rakk->kosong -= $request->jumlah;
            $rakk->save();

            // Ambil data terkait (obat, rak) jika ada relasi
            $transaksiArray['rak'] = $transaksi->rak->nama_rak ?? 'Rak tidak ditemukan';
            $transaksiArray['obat'] = $transaksi->obat->nama_obat ?? 'Obat tidak ditemukan';

            return response()->json([
                'success' => true,
                'message' => 'Data transaksi berhasil ditambahkan',
                'data' => $transaksiArray,

            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal menambahkan data transaksi',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function edittransaksi(Request $request, $id)
    {
        try {
            $rakk = Rak::where('id', $request->idrak)->first();
            if (!$rakk) {
                return response()->json([
                    'success' => false,
                    'message' => 'Rak tidak ditemukan',
                ], 404);
            }
            if ($request->jumlah > $rakk->kosong) {
                return response()->json([
                    'success' => false,
                    'message' => "Kapasitas rak hanya dapat menampung {$rakk->kosong} obat",
                ], 404);
            }


            $transaksi = Transaksi::findOrFail($id);
            $idrakawal = $transaksi->id_rak;
            $rakk2 = Rak::where('id', $idrakawal)->first();
            if ($idrakawal != $request->idrak) {
                $rakk2->terisi -= $transaksi->jumlah;
                $rakk2->kosong += $transaksi->jumlah;
                $rakk2->save();
                $rakk->terisi += $request->jumlah;
                $rakk->kosong -= $request->jumlah;
                $rakk->save();
            } else {
                $rakk->terisi += $request->jumlah;
                $rakk->kosong -= $request->jumlah;
                $rakk->save();
            }

            $transaksi->jumlah = $request->jumlah;
            $transaksi->kadaluarsa = $request->kadaluarsa;
            $transaksi->id_rak = $request->idrak;
            $transaksi->id_obat = $request->idobat;
            $transaksi->masuk = $request->masuk;
            $transaksi->save();

            

            $transaksiArray = $transaksi->toArray();
            $transaksiArray['rak'] = $transaksi->rak->nama_rak ?? 'Rak tidak ditemukan';
            $transaksiArray['obat'] = $transaksi->obat->nama_obat ?? 'Obat tidak ditemukan';

            return response()->json([
                'success' => true,
                'message' => 'Data transaksi berhasil diupdate',
                'data' => $transaksiArray
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal mengupdate data transaksi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function hapustransaksi($id)
    {
        try {
            $transaksi = Transaksi::find($id);
            $rak=Rak::find($transaksi->id_rak);
            $rak->terisi -= $transaksi->jumlah;
            $rak->kosong += $transaksi->jumlah;
            $rak->save();
            if (!$transaksi) {
                return response()->json(['success' => false, 'message' => 'Transaksi tidak ditemukan'], 404);
            }
            $transaksi->delete();
            return response()->json(['success' => true, 'message' => 'Transaksi berhasil dihapus']);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal menghapus data transaksi',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    // tampilkan data kadaluasa
    public function getObatKadaluarsaPerBulan()
    {
        $startDate = Carbon::createFromDate(now()->year, 1, 1);
        $currentMonth = Carbon::now()->format('Y-m');

        // Ambil data kadaluarsa dari database
        $data = Transaksi::selectRaw("DATE_FORMAT(kadaluarsa, '%Y-%m') as bulan, SUM(jumlah) as total")
            ->whereDate('kadaluarsa', '>=', $startDate)
            ->groupByRaw("DATE_FORMAT(kadaluarsa, '%Y-%m')")
            ->orderByRaw("DATE_FORMAT(kadaluarsa, '%Y-%m')")
            ->get();

        // Buat list bulan lengkap
        $bulanRange = [];
        for ($date = $startDate; $date->format('Y-m') <= $currentMonth; $date->addMonth()) {
            $bulanRange[] = $date->format('Y-m');
        }

        // Gabungkan data yang ada dengan bulan kosong
        $finalData = collect($bulanRange)->map(function ($bulan) use ($data) {
            $item = $data->firstWhere('bulan', $bulan);
            return [
                'bulan' => Carbon::createFromFormat('Y-m', $bulan)->translatedFormat('F Y'),
                'total' => $item ? $item->total : 0,
            ];
        });

        return response()->json($finalData);
    }



    public function prediksiObatKadaluarsaBulanDepan()
    {
        // Contoh data historis dummy
        $response = $this->getObatKadaluarsaPerBulan();
        $apiResponse = $response->getData(true);
        $data = [];

        foreach ($apiResponse as $item) {
            // Ubah "Januari 2025" menjadi "2025-01"
            $carbonDate = Carbon::createFromFormat('F Y', $item['bulan']);
            $key = $carbonDate->format('Y-m'); // hasil: "2025-01"
            $data[$key] = $item['total'];
        }

        // Ubah ke array numerik
        $values = array_values($data);

        // Inisialisasi model ARIMA(p,d,q)
        $arima = new ARIMA($values, 1, 1, 1); // Kamu bisa ganti parameternya nanti

        // Prediksi 1 bulan ke depan
        $forecast = $arima->forecast(1);

        $lastMonth = array_key_last($data);
        $nextMonth = Carbon::createFromFormat('Y-m', $lastMonth)->addMonth()->translatedFormat('F Y');

        return response()->json([
            'bulan' => $nextMonth,
            'prediksi' => max(0,(int) round($forecast)),
        ]);
    }



    public function notifikasi()
    {
        $transaksi = Transaksi::with('rak', 'obat')->get();
        if ($transaksi->isEmpty()) {
            return response()->json([
                'message' => 'Data transaksi tidak ditemukan',
                'transaksi' => [] // Ubah key dari 'obat' menjadi 'transaksi'
            ], 404); // Not Found
        }

        return response()->json([
            'message' => 'Data transaksi berhasil diambil',
            'obat' => $transaksi->map(function ($item) {
                return [
                    'id' => $item->id,
                    'jumlah' => $item->jumlah,
                    'tanggal_kadaluarsa' => $item->kadaluarsa,
                    'rak' => $item->rak->nama_rak ?? 'Rak tidak ditemukan',
                    'nama' => $item->obat->nama_obat ?? 'Obat tidak ditemukan',
                    'masuk' => $item->masuk,
                ];
            }),
        ]);
    }

    public function riwayat()
    {
        try {
            // Set locale ke Indonesia
            Carbon::setLocale('id');

            $riwayat = Transaksi::with(['obat', 'rak'])
                ->where('kadaluarsa', '<=', Carbon::now())
                ->orderBy('kadaluarsa', 'desc')
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'kode_obat' => $item->obat->kode,
                        'nama_obat' => $item->obat->nama_obat,
                        'nama_rak' => $item->rak->nama_rak,
                        'jumlah' => $item->jumlah,
                        'tanggal_masuk' => Carbon::parse($item->masuk)->isoFormat('dddd, DD MMMM YYYY'),
                        'tanggal_kadaluarsa' => Carbon::parse($item->kadaluarsa)->isoFormat('dddd, DD MMMM YYYY'),
                    ];
                });

            return response()->json([
                'success' => true,
                'message' => 'Data riwayat obat kadaluarsa',
                'data' => $riwayat
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
