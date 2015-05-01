<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Web extends CI_Controller {

	private $table = 'ios_crash_reports';

	function __construct() {
        parent::__construct();
        $this->load->helper('url');
        $this->load->helper(array('form', 'url'));
        $this->load->database();
    }

	public function index()
	{
		$query = $this->db->query("SELECT * FROM " . $this->table . " ORDER BY report_time DESC");
		$list = $query->result();

		$data = array(
			'list' => $list
			);
		$this->load->view('list', $data);
	}

	public function upload() {
		$lines = file($_FILES['userfile']['tmp_name']);
		//部分导出的日志第一行是json, 删除
		if (substr($lines[0], 0, 1) == '{') {
			array_shift($lines);
		}

		$data = array(
			'report_id' => $this->_get_id($lines), 
			'report_key' => $this->_get_key($lines),
			'device' => $this->_get_device($lines),
			'origin_report' => implode('', $lines),
			'report_time' => $this->_get_time($lines),
			'ios_version' => $this->_get_os_version($lines),
			'uuid' => $this->_get_uuid($lines)
		);

		if ($data['report_id'] === false || $data['report_key'] === false) {
			echo $this->message('日志格式不正确, 无法导入!');
			return;
		}

		$query = $this->db->query('SELECT id FROM '.$this->table." WHERE report_id='${data['report_id']}' LIMIT 1");
		$row = $query->row();

		if ($row) {
			echo $this->message('这个日志已经上传过了? ' . $data['report_id']);
			return;
		}
		$this->db->insert($this->table, $data); 
		echo $this->message('Upload Success!');
	}

	public function origin_report($id) {
		$id = intval($id);
		$query = $this->db->query('SELECT origin_report FROM '.$this->table." WHERE id='$id' LIMIT 1");
		$row = $query->row();
		$data = array('row' => $row->origin_report);
		$this->load->view('view_report', $data);
	}
	public function view_report($id) {
		$id = intval($id);
		$query = $this->db->query('SELECT report FROM '.$this->table." WHERE id='$id' LIMIT 1");
		$row = $query->row();
		$data = array('row' => $row->report);
		$this->load->view('view_report', $data);
	}

	private function message($message) {
		return $message . '<a href="/web/index">Home</a>';
	}

	protected function _get_id($lines) {
		$line = 0;
		$key = 'Incident Identifier:';
		return $this->_get_value($lines, $line, $key);
	}

	protected function _get_key($lines) {
		$line = 1;
		$key = 'CrashReporter Key:';
		return $this->_get_value($lines, $line, $key);
	}

	protected function _get_device($lines) {
		$line = 2;
		$key = 'Hardware Model:';
		return $this->_get_value($lines, $line, $key);
	}

	protected function _get_time($lines) {
		$line = 10;
		$key = 'Date/Time:';
		$str = $this->_get_value($lines, $line, $key);
		return date('Y-m-d H:i:s', strtotime($str));
	}

	protected function _get_os_version($lines) {
		$line = 11;
		$key = 'OS Version:';
		return $this->_get_value($lines, $line, $key);
	}

	protected function _get_uuid($lines){
		$key = "Binary Images:";
		foreach ($lines as $v => $line) {
			if (substr($line, 0, strlen($key)) == $key) {
				$next = $lines[$v + 1];
				return trim(substr($next, strpos($next, '<') + 1, 32));
			}
		}
		return '';
	}

	protected function _get_value($lines, $line, $key) {
		foreach ($lines as $line) {
			if (substr($line, 0, strlen($key)) == $key) {
				return trim(substr($line, strlen($key)));
			}
		}
		//old way
		if (strpos($lines[$line], $key) === false) {
			Log::info('return false');
			return false;
		} else {
			return trim(substr($lines[$line], strlen($key)));
		}
	}
		
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */