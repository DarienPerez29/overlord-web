<?php

namespace App\Controllers\Panel_controllers;
use App\Controllers\BaseController;
use App\Libraries\Permissions;
use App\Libraries\Panel_breadcrumb;

class Users_new extends BaseController {
    private $is_allowed = TRUE;
    private $breadcrumb;

    public function __construct() {
        $session = session();
        $this->breadcrumb = new Panel_breadcrumb();

        if(!Permissions::is_role_allowed(USERS_NEW_TASK, (isset($session->id_rol) ? $session->id_rol : 0))) {
            $this->is_allowed = FALSE;
        }//end if role not allowed
    }//end __construct function

    public function index() {
        if($this->is_allowed){
            return $this->create_view('panel_views/users_new', $this->load_data());
        }//end if not allowed
        else {
            create_user_message('No cuentas con los permisos suficientes para acceder a esta sección...', 'warning');
            return redirect()->to(route_to('panel/dashboard'));
        }//end else not allowed
    }//end index function

    private function load_data() {
        $data = array();
        //Session elements
        $session = session();
        $data['user_name'] = $session->user_name;
        $data['user_full_name'] = $session->user_full_name;
        $data['user_img'] = $session->user_img;
        $data['user_sex'] = $session->user_sex;
        $data['user_role'] = $session->user_rol;

        $data['section_name'] = 'Registrar usuario nuevo';

        //Breadcrumb
        $this->breadcrumb->add_breadcrumb('Dashboard', 'panel/dashboard');
        $this->breadcrumb->add_breadcrumb('Usuarios', 'panel/usuarios');
        $this->breadcrumb->add_breadcrumb('Registrar usuario', 'panel/usuarios/registrar_usuario');
        $data['breadcrumb'] = $this->breadcrumb->generate_breadcrumb();

        //Elementos propios del controller
        $roles_table = new \App\Models\Tabla_rol;
        $data['roles'] = $roles_table->generate_roles_dropdown();

        return $data;
    }//end load_data function

    private function create_view($view_name = '', $content = array()){
        $content['menu'] = generate_nav_menu(USERS_NEW_TASK, session()->id_rol);
        return view($view_name, $content);
    }//end create_view function

    public function insert_user(){
        $user_table = new \App\Models\Tabla_usuario();
        $user = array();
        $user['nombre'] = $this->request->getPost('nombre');
        $user['apellido_p'] = $this->request->getPost('apellido-paterno');
        $user['apellido_m'] = $this->request->getPost('apellido-materno');
        $user['id_rol'] = $this->request->getPost('rol');
        $user['email'] = $this->request->getPost('email');
        $user['sexo'] = $this->request->getPost('sexo');
        // $user['password'] = hash('sha256', $this->request->getPost('confirmar-contrasenia'));
        $user['password'] = $this->request->getPost('confirmar-contrasenia');
        $user['imagen'] = $this->upload_files($this->request->getFile('imagen-perfil'));
        if($user['imagen'] == NULL) {
            $user['imagen'] = 'avatar-none.jpg';
        }

        if(($user_table->insert($user)) > 0){
            create_user_message('El usuario se registro con éxito', 'success');
            return redirect()->to(route_to('panel/usuarios/registrar_usuario'));
        }// end if se inserta usuario
        else {
            create_user_message('Hubo un problema al registrar el usuario. Intenta de nuevo', 'error');
            return redirect()->to(route_to('panel/usuarios/registrar_usuario'));
        }// end else se inserta usuario
    }//end insert_user function

    private function upload_files($file = NULL) {
        $file_name = $file->getRandomName();
        $file_size = $file->getSize();
        if($file_size <= 2097152 && $file_size > 0){
            $file->move('img/users', $file_name);
            return $file_name;
        }//end if file size <= 2 MiB
        else{
            return NULL;
        }//end else file size <= 2 MiB
    }//end upload_files funciton

    // Realizar vista detalles
    // Realizar controlador detalles
    // Realizar ruta get con parámetro (:num), backslash y indes/$1
    // Colocar un link con route_to('ruta', parametro)
    // Generar las tareas para detalles
    // Crear función en método de tabla_usuario
    

}//end User_news class