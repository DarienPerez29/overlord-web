<?php

namespace App\Controllers\Panel_controllers;
use App\Controllers\BaseController;
use App\Libraries\Permissions;
use App\Libraries\Panel_breadcrumb;

class Ins_drums extends BaseController {
    private $is_allowed = TRUE;
    private $breadcrumb;

    public function __construct() {
        $session = session();
        $this->breadcrumb = new Panel_breadcrumb();

        if(!Permissions::is_role_allowed(INS_DRUMS_TASK, (isset($session->id_rol) ? $session->id_rol : 0))){
            $this->is_allowed = FALSE;
        }//end if role not allowed
    }//end __construct function

    public function index() {
        if($this->is_allowed){
            return $this->create_view('panel_views/ins_drums', $this->load_data());
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
        $data['user_img'] = $session->user_img;

        $data['section_name'] = 'Baterías';

        //Breadcrumb
        $this->breadcrumb->add_breadcrumb('Dashboard', 'panel/dashboard');
        $this->breadcrumb->add_breadcrumb('Instrumentos', 'panel/baterias');
        $this->breadcrumb->add_breadcrumb('Baterías', 'panel/baterias');
        $data['breadcrumb'] = $this->breadcrumb->generate_breadcrumb();

        // ==============
        // BATERÍAS TODAS
        // ==============
        $drum_table = new \App\Models\Tabla_bateria;
        $data['drums_all'] = $drum_table->get_datatable_drums();

        return $data;
    }//end load_data function

    private function create_view($view_name = '', $content = array()){
        $content['menu'] = generate_nav_menu(INS_DRUMS_TASK, session()->id_rol);
        return view($view_name, $content);
    }//end create_view function

    public function delete_drum($id_drum = 0){
        $drum_table = new \App\Models\Tabla_bateria();
        if($drum_table->find($id_drum) != NULL){
            $drum_in_db = $drum_table->find($id_drum);
            if($drum_table->delete($id_drum)){
                $this->delete_file($drum_in_db->imagen);
                create_user_message('La batería <b>' . $drum_in_db->marca .' '. $drum_in_db->modelo .' '. $drum_in_db->acabado_color . '</b> ha sido eliminada', 'success');
                return redirect()->to(route_to('panel/baterias'));
            }// end if eliminación de usuario
            else{
                create_user_message('No se ha podido eliminar la batería, intente de nuevo...', 'error');
                return redirect()->to(route_to('panel/baterias'));
            }// end else eliminación de usuario
        }// end if existe usuario
        else{
            create_user_message('La batería a eliminar no existe...', 'error');
            return redirect()->to(route_to('panel/baterias'));
        }
    }// end delete_drum function

    private function delete_file($file = NULL) {
        if(file_exists('img/products/' . $file) && $file != 'd00.jpg'){
            unlink('img/products/' . $file);
        }//end if file exist
    }// end delete_file function
}//end Dashboard class