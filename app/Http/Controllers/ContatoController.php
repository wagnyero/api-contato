<?php

namespace App\Http\Controllers;

use App\Contato;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ContatoController extends Controller
{
    public function create(Request $request) {
        try {
            $params = $request->json()->all();
            $validator = Validator::make($params, [
                "nome"  => "required|string|max:250",
                "canal"  => "required|string|max:20",
                "valor"  => "required|string|max:50",
                "obs"  => "required|string|max:300",
            ]);

            if($validator->fails()) {
                $msgReturn["message"] = $validator->errors()->getMessages();
                $msgReturn["codHttp"] = 400;
            } else {
                $dbContato = new Contato();
                $dbContato->nome  = $params["nome"];
                $dbContato->canal = $params["canal"];
                $dbContato->valor = $params["valor"];
                $dbContato->obs   = $params["obs"];

                $dbContato->save();

                $msgReturn["message"] = "Contato Criado com sucesso";
                $msgReturn["codHttp"] = 201;
            }
        } catch (QueryException $e) {
            $msgReturn["message"] = "Erro ao criar Contato " . $e->getMessage();
            $msgReturn["codHttp"] = 400;
        }

        return response()->json($msgReturn["message"], $msgReturn["codHttp"]);
    }

    public function showAll(Request $request) {
        try {
            $size = 10;
            if($request->size != "") {
                $size = $request->size;
            }

            $msgReturn["message"] = Contato::select("id", "nome", "canal", "valor", "obs")
                                        ->paginate($size);
            $msgReturn["codHttp"] = 200;

        } catch (QueryException $e) {
            $msgReturn["message"] = "Erro ao buscar Contatos " . $e->getMessage();
            $msgReturn["codHttp"] = 401;
        }

        return response()->json($msgReturn["message"], $msgReturn["codHttp"]);
    }

    public function show($id) {
        try {
            $data = Contato::select("id", "nome", "canal", "valor", "obs")
                        ->find($id);

            if(isset($data)) {
                $msgReturn["message"] = $data;
                $msgReturn["codHttp"] = 200;
            } else {
                $msgReturn["message"] = "Nenhum registro encontrado";
                $msgReturn["codHttp"] = 401;
            }
        } catch (QueryException $e) {
            $msgReturn["message"] = "Erro ao buscar Contatos " . $e->getMessage();
            $msgReturn["codHttp"] = 401;
        }

        return response()->json($msgReturn["message"], $msgReturn["codHttp"]);
    }

    public function update(Request $request, $id) {
        try {
            $params = $request->json()->all();
            $validator = Validator::make($params, [
                "nome"  => "required|string|max:250",
                "canal"  => "required|string|max:20",
                "valor"  => "required|string|max:50",
                "obs"  => "required|string|max:300",
            ]);

            if($validator->fails()) {
                $msgReturn["message"] = $validator->errors()->getMessages();
                $msgReturn["codHttp"] = 400;
            } else {
                $dbContato = Contato::find($id);
                if(isset($dbContato)) {
                    $dbContato->nome  = $params["nome"];
                    $dbContato->canal = $params["canal"];
                    $dbContato->valor = $params["valor"];
                    $dbContato->obs   = $params["obs"];

                    $dbContato->save();
                    $msgReturn["message"] = "Contato atualizado";
                    $msgReturn["codHttp"] = 204;
                } else {
                    $msgReturn["message"] = "Erro ao localizar usuário";
                    $msgReturn["codHttp"] = 400;
                }
            }
        } catch (QueryException $e) {
            $msgReturn["message"] = "Erro ao criar Contato " . $e->getMessage();
            $msgReturn["codHttp"] = 401;
        }

        return response()->json($msgReturn["message"], $msgReturn["codHttp"]);
    }

    public function destroy($id) {
        try {
            $registroContato = Contato::find($id);

            if(isset($registroContato)) {
                $registroContato->delete();

                $msgReturn["message"] = "Registro excluído.";
                $msgReturn["codHttp"] = 204;
            } else {
                $msgReturn["message"] = "Erro ao localizar usuário";
                $msgReturn["codHttp"] = 401;    
            }
        } catch (QueryException $e) {
            $msgReturn["message"] = "Erro ao excluir " . $e->getMessage();
            $msgReturn["codHttp"] = 401;
        }

        return response()->json($msgReturn["message"], $msgReturn["codHttp"]);
    }
}
