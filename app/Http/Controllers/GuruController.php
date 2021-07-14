<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GuruModel;
use PhpParser\Node\Stmt\If_;

class GuruController extends Controller
{
    // 
    
    public function __construct()
    {
        $this->GuruModel= new GuruModel();
        $this->middleware('auth');
    }
    
    public function index(){
        $data = [
            'guru' => $this->GuruModel->allData(),
        ];
     return view('v_guru',$data);
    }
    
    public function detail($id_guru)
    {
        if(!$this->GuruModel->detailData($id_guru)){
            abort(404);
        }
        $data = [
            'guru' => $this->GuruModel->detailData($id_guru),
        ];
     return view('v_detailGuru',$data);
    }

    public function add(){
        return view('v_addGuru');
    }
    public function insert(){
        Request()->validate([
            'nip' => 'required|unique:tbl_guru,nip|min:4|max:10',
            'nama_guru' => 'required',
            'mapel' => 'required',
            'mapel' => 'required',
            'foto_guru' => 'required|mimes:jpg,jpeg,bmp,png|max:1024',
        ],[
            'nip.required' => 'NIP wajib diisi !!',
            'nip.unique' => 'NIP sudah digunakan !!',
            'nip.min' => 'Min 4 Karakter !!',
            'nip.max' => 'Max 20 Karakter !!',
            'nama_guru.required' => 'Nama Guru wajib diisi !!',
            'mapel.required' => 'Mata Pelajaran wajib diisi !!',
            'foto_guru.required' => 'Foto Guru wajib diisi !!',
        ]);

        $file = Request()->foto_guru;
        $fileName = Request()->nip . '.' . $file->extension();
        $file->move(public_path('foto_guru'), $fileName) ;

        $data = [
            'nip' => Request()->nip,
            'nama_guru' => Request()->nama_guru,
            'mapel' => Request()->mapel,
            'foto_guru' => $fileName,
        ];
        $this->GuruModel->addData($data);
        return redirect()->route('guru')->with('pesan', 'Data berhasil ditambah');
    }

    public function edit($id_guru){
        if(!$this->GuruModel->detailData($id_guru)){
            abort(404);
        }
        $data = [
            'guru' => $this->GuruModel->detailData($id_guru),
        ];
        return view('v_editGuru',$data);
    }
    public function update($id_guru){
        Request()->validate([
            'nama_guru' => 'required',
            'mapel' => 'required',
            'mapel' => 'required',
            'foto_guru' => 'mimes:jpg,jpeg,bmp,png|max:1024',
        ],[
            'nama_guru.required' => 'Nama Guru wajib diisi !!',
            'mapel.required' => 'Mata Pelajaran wajib diisi !!',
        ]);

        if (Request()->foto_guru<>"") {

            $file = Request()->foto_guru;
            $fileName = Request()->nip . '.' . $file->extension();
            $file->move(public_path('foto_guru'), $fileName) ;
    
            $data = [
                'nip' => Request()->nip,
                'nama_guru' => Request()->nama_guru,
                'mapel' => Request()->mapel,
                'foto_guru' => $fileName,
            ];
    
        }else{

            $data = [
                'nip' => Request()->nip,
                'nama_guru' => Request()->nama_guru,
                'mapel' => Request()->mapel,
            ];
        }
        
        $this->GuruModel->editData($id_guru, $data);
        return redirect()->route('guru')->with('pesan', 'Data berhasil diubah');
    }

    public function delete($id_guru){
        $guru = $this->GuruModel->detailData($id_guru);        
        if ($guru->foto_guru<>"") {
            unlink(public_path('foto_guru') . '/' . $guru->foto_guru);
        }

        $this->GuruModel->deleteData($id_guru);
        return redirect()->route('guru')->with('pesan', 'Data berhasil dihapus');
    }
}
