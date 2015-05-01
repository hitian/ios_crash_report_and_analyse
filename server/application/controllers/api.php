<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Api extends CI_Controller {

	private $table = 'ios_crash_reports';

	function __construct() {
        parent::__construct();
        $this->load->database();
    }

	public function get_ids_list() {
		$query = $this->db->query("SELECT id FROM " . $this->table . " WHERE report IS NULL ORDER BY id DESC");
		$list = $query->result();
		$data = array();
		foreach ($list as $key => $value) {
			$data[] = intval($value->id);
		}
		echo json_encode($data);
	}
	public function get_report_by_id() {
		$id = intval($this->input->get('id'));
		$query = $this->db->query('SELECT origin_report FROM '.$this->table." WHERE id='$id' LIMIT 1");
		$row = $query->row();
		echo $row->origin_report;
	}
	public function upload_by_id() {
		$id = $this->input->post('id');
		$content = $this->input->post('content');
		$update = array('report' => $content);
		$this->db->where('id', $id);
		$this->db->update($this->table, $update);
		echo 'success';
	}
}