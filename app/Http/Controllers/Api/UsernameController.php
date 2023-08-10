<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Faker\Provider\Base;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;


class UsernameController extends Controller
{
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:zip|max:5120',
            'username' => 'required|string|max:191'
        ]);

        if ($validator->fails()) {

            return response()->json([
                'status' => 422,
                'errors' => $validator->messages()
            ], 422);
        } else {

            $archivo = $request->file('file');
            $nombreUsuario = $request->username;
            $carpetaDestino = 'instagram/' . $nombreUsuario;

            // Extraer los documentos del archivo ZIP y guardarlos en la carpeta destino
            $zip = new \ZipArchive;

            if ($zip->open($archivo)) {
                for ($i = 0; $i < $zip->numFiles; $i++) {
                    $nombreDocumento = $zip->getNameIndex($i);
                    $zip->extractTo(storage_path('app/' . $carpetaDestino), $nombreDocumento);
                }
                $zip->close();

                return response()->json([
                    'status' => 200,
                    'message' => 'Files stored successfully'
                ], 200);
            } else {

                return response()->json([
                    'status' => 500,
                    'message' => 'Something went wrong!'
                ], 500);
            }
        }
    }

    public function getDataFollowing($user)
    {
        // abrir el JSOn de los seguidos
        $rutaSeguidos = 'instagram/' . $user . '/followers_and_following/following.json';

        if(Storage::exists($rutaSeguidos)){
            $contenido = Storage::get($rutaSeguidos); 

            if($contenido){
                $seguidos = json_decode($contenido, true);
    
                $array_seguidos = array();

                foreach ($seguidos['relationships_following'] as $data) {

                    $value = $data['string_list_data'][0]['value'];
                    $timestamp = date('Y-m-d', $data['string_list_data'][0]['timestamp']);
                    $link = $data['string_list_data'][0]['href'];
            
                    $array_seguidos[] = array(
                        "user_name" => $value,
                        "enlace" => $link,
                        "date" => $timestamp
                    );
                }

                return $array_seguidos;

            }else{
                echo 'No se pudo acceder al contenido seguidos <br>';
                return false;
            }
            
        }else{
            echo 'No existe la ruta seguidos <br>';
            return false;
        }
        
    }

    public function getDataFollowers($user)
    {
        // abrir el JSOn de los seguidos
        $rutaSeguidores = 'instagram/' . $user . '/followers_and_following/followers_1.json';

        if(Storage::exists($rutaSeguidores)){

            $contenido = Storage::get($rutaSeguidores); 

            if($contenido){
                $seguidores = json_decode($contenido, true);
    
                $array_seguidores = array();

                foreach ($seguidores as $data) {

                    $value = $data['string_list_data'][0]['value'];
                    $timestamp = date('Y-m-d', $data['string_list_data'][0]['timestamp']);
                    $link = $data['string_list_data'][0]['href'];
                
                    $array_seguidores[] = array(
                        "user_name" => $value,
                        "enlace" => $link,
                        "date" => $timestamp
                    );
                }

                return $array_seguidores;

            }else{
                echo 'No se pudo acceder al contenido seguidores <br>';
                return false;
            }
            
        }else{
            echo 'No existe la ruta seguidores <br>';
            return false;
        }
        
    }

    public function getUnfollowers($user)
    {
        $following = $this->getDataFollowing($user);
        $followers = $this->getDataFollowers($user);

        if($following && $followers){

            $unfollowers_users = array_diff(array_column($following, 'user_name'), array_column($followers, 'user_name'));

            $unfollowers_data = array_filter($following, function($item) use ($unfollowers_users) {
                return in_array($item['user_name'], $unfollowers_users);
            });

            $unfollowers = array_values($unfollowers_data);

            return response()->json([
                'status' => 200,
                'unfollowers' => $unfollowers
            ],200);

        }else{
            return response()->json([
                'status' => 404,
                'message' => 'No such files found!'
            ], 404);
        }
    }

    public function getNotFollowing($user)
    {
        $following = $this->getDataFollowing($user);
        $followers = $this->getDataFollowers($user);

        if($following && $followers){

            $unfollowing_users = array_diff(array_column($followers, 'user_name'), array_column($following, 'user_name'));

            $unfollowing_data = array_filter($followers, function($item) use ($unfollowing_users) {
                return in_array($item['user_name'], $unfollowing_users);
            });

            $unfollowing = array_values($unfollowing_data);

            return response()->json([
                'status' => 200,
                'unfollowing' => $unfollowing
            ],200);

        }else{
            return response()->json([
                'status' => 404,
                'message' => 'No such files found!'
            ], 404);
        }
    }
}
