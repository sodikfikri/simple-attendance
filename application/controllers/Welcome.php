<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/userguide3/general/urls.html
	 */
	public function __construct() {
        parent::__construct();
        $this->load->model('report');
        $this->load->model('reportatt');
    }

	public function index()
	{
		$this->load->view('welcome_message');
	}

	public function report() {
		// // get data OUTOVERTIME
		// $outovertime = $this->reportatt->get_data_attparam('OUTOVERTIME');
		// // get data MinsOutOverTime
		// $minsoutovertime = $this->reportatt->get_data_attparam('MinsOutOverTime');
		// // get data NoOutAbsent
		// $nooutabsent = $this->reportatt->get_data_attparam('NoOutAbsent');
		// // get data LEAVECLASS1
		// $leaveclass = $this->reportatt->get_data_leaveclass();
        $this->load->view('report');
    }

	private function get_date_range($start_date, $end_date) {
		$period = new DatePeriod(
            new DateTime($start_date),
            new DateInterval('P1D'),
            (new DateTime($end_date))->modify('+1 day')
        );

        $dates = [];
        foreach ($period as $date) {
            $dates[] = $date->format('Y-m-d');
        }

        return $dates;
	}

	public function get_userinfo() {
		$data = $this->reportatt->get_all_user();
		$this->output
			->set_content_type('application/json')
			->set_output(json_encode([
				'meta' => [
					'code' => 200,
					'message' => 'Success'
				],
				'data' => $data
			]));
		return;
	}

	private function is_time_between($time, $start, $end) { $time = strtotime($time);
		$start = strtotime($start);
		$end = strtotime($end);
		
		if ($start <= $end) {
			return $time >= $start && $time <= $end;
		} else { // crosses midnight
			return $time >= $start || $time <= $end;
		}
	}

	private function find_shift($checkin, $checkout, $shifts) {
		foreach ($shifts as $shift) {
			if ($this->is_time_between($checkin, (new DateTime($shift->CHECKINTIME1))->format('H:i:s'), (new DateTime($shift->CHECKINTIME2))->format('H:i:s')) &&
				$this->is_time_between($checkout, (new DateTime($shift->CHECKOUTTIME1))->format('H:i:s'), (new DateTime($shift->CHECKOUTTIME2))->format('H:i:s'))) {
				return $shift;
			}
		}
		return null;
	}

	private function get_schclass_by_shift_auto($data, $start_time, $end_time) {
		// get schclass
		$schclass = [];

		foreach($data as $item) {
			$get_sch = $this->reportatt->get_schclass_byid($item->SchId);
			array_push($schclass, $get_sch);
		}

		// $shift = $this->find_shift($start_time, $end_time, $schclass);
		
		return $schclass;
	}

	public function generate_report() {

		$user_id = $this->input->get('user_id');
		$start_date = $this->input->get('start_date');
		$end_date = $this->input->get('end_date');
		
		$usid = explode(",", $user_id);
		
		$get_date_range = $this->get_date_range($start_date, $end_date);
		
		$data = [];
		
		for($n = 0; $n < count($usid); $n++) {
			// get user info
			$userinfo = $this->reportatt->get_userinfo_byid($usid[$n]);

			$cek_user_used_class = $this->reportatt->get_user_used_class($usid[$n]);
			
			$employee_sch = [];

			if (count($cek_user_used_class) === 0) {
				// dapatkan data data schclass
				foreach($get_date_range as $key => $dt_range) {
					// cek apakah karyawan masuk shift sementara
					$ck_usr_tmp = $this->reportatt->check_user_tmp_sch($usid[$n], $dt_range);

					if (count($ck_usr_tmp) != 0) {
						$get_sch = $this->reportatt->get_schclass_byid($ck_usr_tmp[0]->SCHCLASSID);
						
						$get_sch->STARTTIME = (new DateTime($get_sch->STARTTIME))->format('H:i:s');
						$get_sch->ENDTIME = (new DateTime($get_sch->ENDTIME))->format('H:i:s');
						$get_sch->employee_name = $userinfo[0]->Name;
						$get_sch->employee_no = $userinfo[0]->USERID;
						$get_sch->employee_no_akun = $userinfo[0]->Badgenumber;
						$get_sch->date = $dt_range;
						$get_sch->COMETIME = $ck_usr_tmp[0]->COMETIME;
						$get_sch->LEAVETIME = $ck_usr_tmp[0]->LEAVETIME;
						$get_sch->is_user_temp_sch = 'yes';
						$get_sch->is_holiday = false;
						$get_sch->interval_checkinout = '';

						array_push($employee_sch, $get_sch);
					} else {
						// get date N
						$N_day_of_week = date('N', strtotime($dt_range));
						// get data user of run
						$user_of_run = $this->reportatt->get_data_user_of_run_by_userid($usid[$n]);
						// get data num run
						$num_run = $this->reportatt->get_data_num_run($user_of_run[0]->NUM_OF_RUN_ID);
						// get data num run deil
						$num_run_deil = $this->reportatt->get_data_num_run_deil($num_run[0]->NUM_RUNID, $N_day_of_week);
						
						if (count($num_run_deil) != 0) {
							// get sch class id
							$get_sch = $this->reportatt->get_schclass_byid($num_run_deil[0]->SCHCLASSID);
							$get_sch->STARTTIME = (new DateTime($get_sch->STARTTIME))->format('H:i:s');
							$get_sch->ENDTIME = (new DateTime($get_sch->ENDTIME))->format('H:i:s');
							$get_sch->employee_name = $userinfo[0]->Name;
							$get_sch->employee_no = $userinfo[0]->USERID;
							$get_sch->employee_no_akun = $userinfo[0]->Badgenumber;
							$get_sch->date = $dt_range;
							$get_sch->is_user_temp_sch = 'no';
							$get_sch->is_holiday = false;
							$get_sch->interval_checkinout = '';

							array_push($employee_sch, $get_sch);
						} else {
							$ck_checkinout = $this->reportatt->get_checkinout_bydate($usid[$n], $dt_range);

							if (count($ck_checkinout) !== 0) {
								$s_work = (new DateTime($ck_checkinout[0]->CHECKTIME))->format('H:i:s');
								$e_work = (new DateTime(end($ck_checkinout)->CHECKTIME))->format('H:i:s');;

								$obj = [
									'SCHNAME' => 'JADWAL KERJA MANUAL',
									'employee_name' => $userinfo[0]->Name,
									'employee_no' => $userinfo[0]->USERID,
									'employee_no_akun' => $userinfo[0]->Badgenumber,
									'date' => $dt_range,
									'is_user_temp_sch' => 'yes',
									'STARTTIME' => '',
									'ENDTIME' => '',
									'employee_checkin_time' => $s_work,
									'employee_checkout_time' => $e_work,
									'late_time' => '',
									'home_early' => '',
									'empty_data' => false,
									'is_holiday' => false
								];

								array_push($employee_sch, $obj);
							}
						}
					}
				}
				// calculate data
				foreach($employee_sch as $key => $item) {
					if (isset($item->SCHNAME)) {
						# code...
						$start_work = (new DateTime($item->STARTTIME))->format('H:i:s');
						$end_work = (new DateTime($item->ENDTIME))->format('H:i:s');

						$interval_start_work = new DateTime($start_work);
						$interval_end_work = new DateTime($end_work);

						$time_diff_work = $interval_start_work->diff($interval_end_work);
						$interval_work = $time_diff_work->format('%H:%I:%S');
						
						$employee_sch[$key]->interval_work = $interval_work;

						if (isset($item->LATEMINUTES) && $item->LATEMINUTES > 0) {
							$start_work = (new DateTime($start_work))->add(new DateInterval('PT'.$item->LATEMINUTES.'M'))->format('H:i:s');
						}
						
						if (isset($item->EARLYMINUTES) && $item->EARLYMINUTES > 0) {
							$end_work = (new DateTime($end_work))->sub(new DateInterval('PT'.$item->EARLYMINUTES.'M'))->format('H:i:s');
						}

						$checkin_time_second = (new DateTime($item->CHECKINTIME2))->format('H:i:s');
						
						$get_checkinout = $this->reportatt->get_checkinout_bydate($usid[$n], $item->date);
						
						if (count($get_checkinout) != 0) {
							$employee_sch[$key]->empty_data = false;

							usort($get_checkinout, function($a, $b) {
								return strtotime($a->CHECKTIME) - strtotime($b->CHECKTIME);
							});

							// get check in data
							if ((new DateTime($get_checkinout[0]->CHECKTIME))->format('H:i:s') < $checkin_time_second) {

								// print_r('masuk ke sini');
								$firstTime = $get_checkinout[0]->CHECKTIME;
								$firstDateTime = new DateTime($firstTime);
								$first_thresholdTime = new DateTime($firstDateTime->format('Y-m-d') . $start_work);
								
								$employee_sch[$key]->employee_checkin = $firstTime;
								$employee_sch[$key]->employee_checkin_time = (new DateTime($firstTime))->format('H:i:s');

								if ($firstDateTime > $first_thresholdTime) {
									$interval = $firstDateTime->diff($first_thresholdTime);
									$employee_sch[$key]->late_time = $interval->format('%H:%I:%S');
								} else {
									$employee_sch[$key]->late_time = '';
								}
							}

							$end_ckio = end($get_checkinout);

							if ((new DateTime($end_ckio->CHECKTIME))->format('H:i:s') > (new DateTime($employee_sch[$key]->CHECKOUTTIME1))->format('H:i:s')) {
								$lastTime = $end_ckio->CHECKTIME;
								$lastDateTime = new DateTime($lastTime);
								$last_thresholdTime = new DateTime($lastDateTime->format('Y-m-d') . $end_work);
								
								$employee_sch[$key]->employee_checkout = $lastTime;
								$employee_sch[$key]->employee_checkout_time = (new DateTime($lastTime))->format('H:i:s');

								if ($lastDateTime < $last_thresholdTime) {
									$interval = $lastDateTime->diff($last_thresholdTime);
									$employee_sch[$key]->home_early = $interval->format('%H:%I:%S');
								} else {
									$employee_sch[$key]->home_early = '';
								}
							}
							
							if (isset($employee_sch[$key]->employee_checkin) && isset($employee_sch[$key]->employee_checkout_time)) {
								$interval_start_ckio = new DateTime($employee_sch[$key]->employee_checkin);
								$interval_end_ckio = new DateTime($employee_sch[$key]->employee_checkout_time);

								$time_diff_ckio = $interval_start_ckio->diff($interval_end_ckio);
								$interval_ckio = $time_diff_ckio->format('%H:%I:%S');
								$employee_sch[$key]->interval_checkinout = $interval_ckio;
							}

						} else {
							$holidasy = $this->reportatt->get_holidays($item->date);
							if (count($holidasy) != 0) {
								# code...
								$employee_sch[$key]->is_holiday = true;
								$employee_sch[$key]->holiday_name = $holidasy[0]->HOLIDAYNAME;
							}
							$employee_sch[$key]->empty_data = true;
						}
					}
				}
			} else {

				$is_changes_day = false;
				foreach($get_date_range as $key => $dt_range) {
					$schclass_used = $this->get_schclass_by_shift_auto($cek_user_used_class, '', '');

					foreach($schclass_used as $keyused => $item) {
						$found_sch = $this->reportatt->get_schclass_byid($schclass_used[$keyused]->SCHCLASSID);
						
						if ($found_sch) {
							/*  
								* NOTES
								* Jika karyawan masuk kerja shift malam dan pada esok harinya dia langsung masuk shift pagi maka
								* jarak antara jam checkout pada shift malam dan jam checkin pada shift pagi harus >= 2 jam karena untuk sekarang
								* jam kerja shift tidak boleh tumpang tindih (ini masih perlu diskusi dengan pak iwan apakan range >= 2 jam itu sudah sesuai atau tidak)
							*/
							$checkintime1 = $is_changes_day ? (new DateTime($found_sch->CHECKINTIME1))->modify('+2 hours')->format('H:i:s') : (new DateTime($found_sch->CHECKINTIME1))->format('H:i:s');
							$checkintime2 = (new DateTime($found_sch->CHECKINTIME2))->format('H:i:s');
							$checkouttime1 = (new DateTime($found_sch->CHECKOUTTIME1))->format('H:i:s');
							$checkouttime2 = (new DateTime($found_sch->CHECKOUTTIME2))->format('H:i:s');
							$starttime = (new DateTime($found_sch->STARTTIME))->format('H:i:s');

							$ckin_start = null;
							$ckin_end = null;
							$ckout_start = null;
							$ckout_end = null;

							if ($checkintime1 <= $checkintime2) {
								$ckin_start = "$dt_range $checkintime1";
								$ckin_end = "$dt_range $checkintime2";

								$ckout_start = "$dt_range $checkouttime1";
								$ckout_end = "$dt_range $checkouttime2";

								$is_changes_day = false;
							} else {
								if ($checkintime1 <= $starttime) {
									$ckin_start = "$dt_range $checkintime1";
									$ckin_end = (new DateTime($dt_range))->modify('+1 day')->format('Y-m-d') . ' ' .  $checkintime2;

									$ckout_start = (new DateTime($dt_range))->modify('+1 day')->format('Y-m-d') . ' ' . $checkouttime1;
									$ckout_end = (new DateTime($dt_range))->modify('+1 day')->format('Y-m-d') . ' ' .  $checkouttime2;

								} else {
									// untuk condisi else ini masih perlu di pastikan akan masuk shift mana
									print_r(json_encode('masuk ke condisi else'));
									return;

									$ckin_start = (new DateTime($dt_range))->modify('-1 day')->format('Y-m-d') . ' ' . $checkintime1;
									$ckin_end = "$dt_range $checkintime2";
								}
							}

							// get checkin data
							$get_checkin = $this->reportatt->get_checkinout_byhour($usid[$n], $ckin_start, $ckin_end);
							if ($get_checkin) {
								$schclass_used[$keyused]->employee_checkin = (new DateTime($get_checkin[0]->CHECKTIME))->format('H:i:s');
								$schclass_used[$keyused]->employee_checkin_time = (new DateTime($get_checkin[0]->CHECKTIME))->format('H:i:s');
								$schclass_used[$keyused]->date = $dt_range;
								$schclass_used[$keyused]->employee_name = $userinfo[0]->Name;
								$schclass_used[$keyused]->employee_no = $userinfo[0]->USERID;
								$schclass_used[$keyused]->employee_no_akun = $userinfo[0]->Badgenumber;
								$schclass_used[$keyused]->is_user_temp_sch = 'no';
							}

							$get_checkout = $this->reportatt->get_checkinout_byhour($usid[$n], $ckout_start, $ckout_end);
							if ($get_checkout) {
								$schclass_used[$keyused]->employee_checkout_time = (new DateTime($get_checkout[0]->CHECKTIME))->format('H:i:s');
								$schclass_used[$keyused]->date = $dt_range;
								$schclass_used[$keyused]->employee_name = $userinfo[0]->Name;
								$schclass_used[$keyused]->employee_no = $userinfo[0]->USERID;
								$schclass_used[$keyused]->employee_no_akun = $userinfo[0]->Badgenumber;
								$schclass_used[$keyused]->is_user_temp_sch = 'no';
							}

							if (count($get_checkin) !== 0) {
								if ((new DateTime($schclass_used[$keyused]->STARTTIME))->format('H:i:s') > '22:00:00') {
									$is_changes_day = true;
								}
								array_push($employee_sch, $schclass_used[$keyused]);
								break;
							}
						}
					}
				}

				foreach($employee_sch as $key_emp => $item_emp) {
					
					$employee_sch[$key_emp]->interval_work = $this->interval_format($item_emp->STARTTIME, $item_emp->ENDTIME);

					if (isset($employee_sch[$key_emp]->employee_checkin_time)) {
						// print_r('masuk ke sini');
						$firstTime = $employee_sch[$key_emp]->employee_checkin_time;
						$firstDateTime = new DateTime($firstTime);
						$first_thresholdTime = new DateTime($firstDateTime->format('Y-m-d') . (new DateTime($employee_sch[$key_emp]->STARTTIME))->format('H:i:s'));

						if ($firstDateTime > $first_thresholdTime) {
							$interval = $firstDateTime->diff($first_thresholdTime);
							$employee_sch[$key_emp]->late_time = $interval->format('%H:%I:%S');
						} else {
							$employee_sch[$key_emp]->late_time = '';
						}
					}

					if (isset($employee_sch[$key_emp]->employee_checkout_time)) {
						
						$lastTime = $employee_sch[$key_emp]->employee_checkout_time;
						$lastDateTime = new DateTime($lastTime);
						$last_thresholdTime = new DateTime($lastDateTime->format('Y-m-d') . (new DateTime($employee_sch[$key_emp]->ENDTIME))->format('H:i:s'));

						if ($lastDateTime < $last_thresholdTime) {
							$interval = $lastDateTime->diff($last_thresholdTime);
							$employee_sch[$key_emp]->home_early = $interval->format('%H:%I:%S');
						} else {
							$employee_sch[$key_emp]->home_early = '';
						}
					}

					if (isset($employee_sch[$key_emp]->employee_checkin_time) && isset($employee_sch[$key_emp]->employee_checkout_time)) {
						$interval_start_ckio = $employee_sch[$key_emp]->employee_checkin_time;
						$employee_sch[$key_emp]->interval_checkinout = $this->interval_format($employee_sch[$key_emp]->employee_checkin_time, $employee_sch[$key_emp]->employee_checkout_time);
					}

					$employee_sch[$key_emp]->STARTTIME = (new DateTime($employee_sch[$key_emp]->STARTTIME))->format('H:i:s');
					$employee_sch[$key_emp]->ENDTIME = (new DateTime($employee_sch[$key_emp]->ENDTIME))->format('H:i:s');
					
				}

			}

			array_push($data, $employee_sch);
		}
		
		$final_data = [];
		foreach($data as $kd => $vd) {
			foreach($vd as $itm) {
				array_push($final_data,$itm);
			}
		}
		
		$this->output
			->set_content_type('application/json')
			->set_output(json_encode([
				'meta' => [
					'code' => 200,
					'message' => 'Success'
				],
				'data' => $final_data
			]));
		return;
	}

	private function interval_format($start_time, $end_time) {
		$start_work = (new DateTime($start_time))->format('H:i:s');
		$end_work = (new DateTime($end_time))->format('H:i:s');
		// Konversi waktu ke detik sejak awal hari
		$seconds1 = strtotime($start_work) - strtotime("00:00:00");
		$seconds2 = strtotime($end_work) - strtotime("00:00:00");
		// Jika waktu akhir lebih kecil dari waktu awal, tambahkan 24 jam ke waktu akhir
		if ($seconds2 < $seconds1) {
			$seconds2 += 24 * 3600; // 24 jam dalam detik
		}
		// Hitung selisih waktu dalam detik
		$intervalSeconds = $seconds2 - $seconds1;
		// Konversi selisih detik ke jam, menit, dan detik
		$hours = floor($intervalSeconds / 3600);
		$minutes = floor(($intervalSeconds % 3600) / 60);
		$seconds = $intervalSeconds % 60;
		$intervalFormatted = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);

		return $intervalFormatted;
	}

	public function dayoffapi() {
		// Curl Init
		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => 'https://dayoffapi.vercel.app/api?year='.$this->input->get('year'),
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'GET',
			CURLOPT_HTTPHEADER => array(
				'Content-Type: application/x-www-form-urlencoded'
			),
		));

		$response = curl_exec($curl);

		curl_close($curl);

		// array_push($response, [
		// 	"tanggal" => "2017-01-1",
		// 	"keterangan" => "Tahun Baru 2017 Masehi",
		// 	"is_cuti" => false
		// ]);

		// print_r(json_decode($response));
		// return;

		$this->output
			->set_content_type('application/json')
			->set_output(json_encode([
				'meta' => [
					'code' => 200,
					'message' => 'Success'
				],
				'data' => json_decode($response)
			]));
		return;
	}

}
