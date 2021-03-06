<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use DB;
use App\Tutorial;
use App\Setor; 
use Gate;

//Log
use App\Http\Controllers\Log;
use App\Http\Controllers\LogController;

class TutorialController extends Controller
{
    /* ----------------------- LOGS ----------------------*/

    private function log($info){
        //path name
        $filename="TutorialController";

        $log = new LogController;
        $log->store($filename, $info);
        return null;     
    }

    /* ----------------------- END LOGS --------------------*/

    //
    private $tutorial;

    public function __construct(Tutorial $tutorial){
        $this->tutorial = $tutorial;        
    }

    public function index($setor){
        $my_setor = $setor;

        if(!(Gate::denies('read_'.$setor))){ 

            $setor = Setor::where('name', $setor)->first();


            $tutorials = $setor->tutorials()
                                ->orderBy('id', 'DESC')
                                ->paginate(40);

            //LOG ----------------------------------------------------------------------------------------
            $this->log("tutorial.index");
            //--------------------------------------------------------------------------------------------

            return view('tutorial.index', array('tutorials' => $tutorials, 'buscar' => null, 'setor_atual' => $setor));
        }
        else{
            return redirect('erro')->with('permission_error', '403');
        }
    }

    public function busca (Request $request, $setor){
        $my_setor = $setor;

        if(!(Gate::denies('read_'.$setor))){ 


            $setor = Setor::where('name', $setor)->first();

            $setor_id = $setor->id;

            $buscaInput = $request->input('busca');

            $tutorials = $setor->tutorials()
                                ->where(function($query) use ($buscaInput) {
                                    $query->where('titulo', 'LIKE', '%'.$buscaInput.'%')
                                    ->orwhere('palavras_chave', 'LIKE', '%'.$buscaInput.'%')
                                    ->orwhere('conteudo', 'LIKE', '%'.$buscaInput.'%');
                                })
                                ->orderBy('id', 'DESC')
                                ->paginate(40);
            

            //LOG ----------------------------------------------------------------------------------------
            $this->log("tutorial.ibusca=".$buscaInput);
            //--------------------------------------------------------------------------------------------

            return view('tutorial.index', array('tutorials' => $tutorials, 'buscar' => $buscaInput, 'setor_atual' => $setor ));
        }
        else{
            return redirect('erro')->with('permission_error', '403');
        }
    }

    // Seleciona por id
    public function show($setor, $id){

        $my_setor = $setor;

        if(!(Gate::denies('read_'.$setor))){ 
            $tutorial = Tutorial::find($id);

            /* ------------------------------ Security --------------------------------*/
            //verifica se o setor tem permissão ao tutorial
            $setors_security = $tutorial->setors()->where('name', $setor)->first();

            if(!(isset($setors_security->id))){
                return redirect('erro')->with('permission_error', '403');
            }
            /* ------------------------------ END Security --------------------------------*/

            //LOG ----------------------------------------------------------------------------------------
            $this->log("tutorial.show.id=".$id);
            //--------------------------------------------------------------------------------------------

            return view('tutorial.show', compact('tutorial', 'setor'));
        }
        else{
            return redirect('erro')->with('permission_error', '403');
        }

    }

    

    // Criar
    public function create($setor){
        $my_setor = $setor;

        if(!(Gate::denies('create_'.$setor))){

            $setores = Setor::all();

            //LOG ----------------------------------------------------------------------------------------
            $this->log("tutorial.create");
            //--------------------------------------------------------------------------------------------

            $setor_atual = $setor;

            return view('tutorial.create', compact('setores', 'setor_atual'));                  
        }
        else{
            return redirect('erro')->with('permission_error', '403');
        }
    }

    // Criar usuário
    public function store(Request $request, $setor){

        $setor_atual = $setor;

        if(!(Gate::denies('create_'.$setor))){
            //Validação
            $this->validate($request,[
                    'titulo' => 'required|min:3',
                    'palavras_chave' => 'required|min:3',
                    'conteudo' => 'required|min:3',
                    'setor' => 'required',               
            ]);

            
                    
            $tutorial = new Tutorial();
            $tutorial->titulo = $request->input('titulo');
            $tutorial->palavras_chave = $request->input('palavras_chave');
            $tutorial->conteudo = $request->input('conteudo');

            //LOG ----------------------------------------------------------------------------------------
            $this->log("tutorial.store");
            //--------------------------------------------------------------------------------------------

            if($tutorial->save()){

                $setores = $request->input('setor');

                $tutorial_id = DB::getPdo()->lastInsertId();
                //Vincula tecnicos ao livro
                foreach ($setores as $setor) {
                    Tutorial::find($tutorial_id)->setors()->attach($setor);
                }
                
                return redirect('tutorials/'.$setor_atual)->with('success', 'Tutorial  cadastrada com sucesso!');
            }else{
                return redirect('tutorials/'.$id.'/edit')->with('danger', 'Houve um problema, tente novamente.');
            }
        }
        else{
            return redirect('erro')->with('permission_error', '403');
        }

    }

    public function edit($setor, $id){  
        if(!(Gate::denies('update_'.$setor))){      
            $tutorial = Tutorial::find($id);

            /* ------------------------------ Security --------------------------------*/
            //verifica se o setor tem permissão ao tutorial
            $setors_security = $tutorial->setors()->where('name', $setor)->first();

            if(!(isset($setors_security->id))){
                return redirect('erro')->with('permission_error', '403');
            }
            /* ------------------------------ END Security --------------------------------*/

            //LOG ----------------------------------------------------------------------------------------
            $this->log("tutorial.edit.id=".$id);
            //--------------------------------------------------------------------------------------------

            return view('tutorial.edit', compact('tutorial','id', 'setor'));
        }
        else{
            return redirect('erro')->with('permission_error', '403');
        }
         


    }

    public function update(Request $request, $setor, $id){

        if(!(Gate::denies('update_'.$setor))){      
            $tutorial = Tutorial::find($id);

            /* ------------------------------ Security --------------------------------*/
            //verifica se o setor tem permissão ao tutorial
            $setors_security = $tutorial->setors()->where('name', $setor)->first();

            if(!(isset($setors_security->id))){
                return redirect('erro')->with('permission_error', '403');
            }
            /* ------------------------------ END Security --------------------------------*/

            //Validação
            $this->validate($request,[
                    'titulo' => 'required|min:3',
                    'palavras_chave' => 'required|min:3',
                    'conteudo' => 'required|min:3',     
            ]);
                    
            $tutorial->titulo = $request->input('titulo');
            $tutorial->palavras_chave = $request->input('palavras_chave');
            $tutorial->conteudo = $request->input('conteudo');

            //LOG ----------------------------------------------------------------------------------------
            $this->log("tutorial.update.id=".$id);
            //--------------------------------------------------------------------------------------------      

            if($tutorial->save()){
                return redirect('tutorials/'.$setor)->with('success', 'Tutorial  atualizada com sucesso!');
            }else{
                return redirect('tutorials/'.$id.'/edit')->with('danger', 'Houve um problema, tente novamente.');
            }
        }
        else{
            return redirect('erro')->with('permission_error', '403');
        }

    }

    public function destroy($setor, $id){

        if(!(Gate::denies('delete_'.$setor))){
            $tutorial = Tutorial::find($id);    

            /* ------------------------------ Security --------------------------------*/
            //verifica se o setor tem permissão ao tutorial
            $setors_security = $tutorial->setors()->where('name', $setor)->first();

            if(!(isset($setors_security->id))){
                return redirect('erro')->with('permission_error', '403');
            }
            /* ------------------------------ END Security --------------------------------*/    
            
            $tutorial->delete();


            //LOG ----------------------------------------------------------------------------------------
            $this->log("tutorial.destroy.id=".$id);
            //--------------------------------------------------------------------------------------------

            return redirect()->back()->with('success','Tutorial excluída com sucesso!');
        }
        else{
            return redirect('erro')->with('permission_error', '403');
        }
    }

    public function setors($setor, $id){

        $my_setor = $setor;

        if(!(Gate::denies('read_'.$setor))){  


            $tutorial = $this->tutorial->find($id);

            /* ------------------------------ Security --------------------------------*/
            //verifica se o setor tem permissão ao tutorial
            $setors_security = $tutorial->setors()->where('name', $setor)->first();

            if(!(isset($setors_security->id))){
                return redirect('erro')->with('permission_error', '403');
            }
            /* ------------------------------ END Security --------------------------------*/


            //recuperar setors
            $setors = $tutorial->setors()->get();

            //todos setores
            $all_setors = Setor::all();

            //LOG ----------------------------------------------------------------------------------------
            $this->log("tutorial.setor.tutorial:".$id);
            //--------------------------------------------------------------------------------------------


            return view('tutorial.setor', compact('tutorial', 'setors', 'all_setors', 'my_setor'));
        }
        else{
            return redirect('erro')->with('permission_error', '403');
        }

    }

    public function setorUpdate(Request $request){
            $my_setor = $request->input('my_setor'); 

        if(!(Gate::denies('update_'.$my_setor))){              
                    
            $setor_id = $request->input('setor_id');
            $tutorial_id = $request->input('tutorial_id');

            $tutorial  = Tutorial::find($tutorial_id);

            /* ------------------------------ Security --------------------------------*/
            //verifica se o setor tem permissão ao tutorial
            $setors_security = $tutorial->setors()->where('name', $my_setor)->first();

            if(!(isset($setors_security->id))){
                return redirect('erro')->with('permission_error', '403');
            }
            /* ------------------------------ END Security --------------------------------*/

            $status = Setor::find($setor_id)->setorTutorial()->attach($tutorial->id);

            //LOG ----------------------------------------------------------------------------------------
            $this->log("tutorial.setorUpdate.setor_id:".$setor_id."tutorial_id:".$tutorial_id);
            //--------------------------------------------------------------------------------------------
          
            if(!$status){
                return redirect('tutorials/'.$my_setor."/".$tutorial_id.'/setors')->with('success', 'Setor  atualizada com sucesso!');
            }else{
                return redirect('tutorials/'.$my_setor."/".$tutorial_id.'/setors')->with('danger', 'Houve um problema, tente novamente.');
            }
        }
        else{
            return redirect('erro')->with('permission_error', '403');
        }

    }


    public function setorDestroy(Request $request){

        $my_setor = $request->input('my_setor'); 

        if(!(Gate::denies('delete_'.$my_setor))){
            $setor_id = $request->input('setor_id');
            $tutorial_id = $request->input('tutorial_id');  

            $tutorial = Tutorial::find($tutorial_id);

            /* ------------------------------ Security --------------------------------*/
            //verifica se o setor tem permissão ao tutorial
            $setors_security = $tutorial->setors()->where('name', $my_setor)->first();

            if(!(isset($setors_security->id))){
                return redirect('erro')->with('permission_error', '403');
            }
            /* ------------------------------ END Security --------------------------------*/

            $setor = Setor::find($setor_id);

            $status = $setor ->setorTutorial()->detach($tutorial->id);

            //LOG ----------------------------------------------------------------------------------------
            $this->log("tutorial.setorDestroy.setor:".$setor->name);
            //--------------------------------------------------------------------------------------------

            
            if($status){
                return redirect('tutorials/'.$my_setor."/".$tutorial_id.'/setors')->with('success', 'Setor  atualizada com sucesso!');
            }else{
                return redirect('tutorials/'.$my_setor."/".$tutorial_id.'/setors')->with('danger', 'Houve um problema, tente novamente.');
            }
        }
        else{
            return redirect('erro')->with('permission_error', '403');
        }
    }
}
