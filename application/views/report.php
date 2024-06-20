<?php
// print_r('<pre>');
// print_r($data);
// print_r('</pre>');
// die;
?>

<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/css/dataTables.bootstrap.min.css" integrity="sha512-BMbq2It2D3J17/C7aRklzOODG1IQ3+MHw3ifzBHMBwGO/0yUqYmsStgBjI0z5EYlaDEFnvYV7gNYdD3vFLRKsA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script data-search-pseudo-elements defer src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <title>Attendance</title>
  </head>
  <body>
    <div class="container-fluid mt-4">
        <h3>Report Attendance</h3>
        <hr>
        <div class="card">
          <div class="card-body">
            <div class="row">
              <div class="col-4">
                <select class="form-control" multiple="multiple" name="" id="user-id">
                  <!-- <option value="79">Hari Suwanto</option>
                  <option value="80" selected>Wilis Rahparela, A.Md. A</option> -->
                </select>
              </div>
              <div class="col-2">
                <input type="text" class="form-control" name="daterange" id="daterange" value="" />
              </div>
              <div class="col-2">
                <button class="btn btn-primary" id="btn-search">Search</button>
              </div>
            </div>
          </div>
        </div>
        <div class="table-responsive mt-3">
            <table class="table table-bordered table-striped display nowrap" style="width: 100%;">
                <thead>
                    <tr>
                        <th scope="col">Emp No</th>
                        <th scope="col">No Akun</th>
                        <th scope="col">Nama</th>
                        <th scope="col">Jadwal Otomatis</th>
                        <th scope="col">Tanggal</th>
                        <th scope="col">Jam Kerja</th>
                        <th scope="col">Mulai Tugas</th>
                        <th scope="col">Akhir Tugas</th>
                        <th scope="col">Lama Tugas</th>
                        <th scope="col">Masuk</th>
                        <th scope="col">Pulang</th>
                        <th scope="col">Lama Bekerja</th>
                        <th scope="col">Telat</th>
                        <th scope="col">Pulang Awal</th>
                        <th scope="col">Bolos</th>
                        <th scope="col">Hari Libur</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                      <td id="no-data" colspan="16" class="text-center">data tidak ditemukan! </i></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Optional JavaScript; choose one of the two! -->

    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/js/jquery.dataTables.min.js" integrity="sha512-BkpSL20WETFylMrcirBahHfSnY++H2O1W+UnEEO4yNIl+jI2+zowyoGJpbtk6bx97fBXf++WJHSSK2MV4ghPcg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <!-- Memuat Date Range Picker -->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
      jQuery(function($) {

        $('#user-id').select2()

        const getuser = () => {
          $.ajax({
            url: 'http://localhost/InterActive/simple-att/index.php/welcome/get_userinfo',
            method: 'GET',
            success: function(resp) {
              $.each(resp.data, function(key, val) {
                // if (key < 10) {
                  var newOption = new Option(`${val.Badgenumber} - ${val.Name}`,val.USERID, false, false);
                  $('#user-id').append(newOption).trigger('change');
                // }
              })
            }
          })
        }
        getuser()

        $('input[name="daterange"]').daterangepicker({
            opens: 'right'
        });
        
        const list_data = (user_id, start_date, end_date) => {

          $('.table').DataTable({
            bDestroy: true,
            pageLength: 100000,
            dom: "Bfrtip",
            scrollX: true,
            ajax: {
              url: `http://localhost/InterActive/simple-att/index.php/welcome/generate_report?user_id=${user_id}&start_date=${start_date}&end_date=${end_date}`,
              method: 'GET',
            },
            columns: [
              { 
                data: 'employee_no',
                render: function(data, type, row) {
                  return data ?? ''
                }
              },
              { 
                data: 'employee_no_akun',
                render: function(data, type, row) {
                  return data ?? ''
                }
              },
              { 
                data: 'employee_name',
                render: function(data, type, row) {
                  return data ?? ''
                }
              },
              {
                data: 'is_user_temp_sch',
                render: function(data, type, row) {
                  return data == 'no' ? '' : '<i class="fa fa-check" style="color: red"></i>'
                },
                className: 'text-center'
              },
              { 
                data: 'date',
                render: function(data, type, row) {
                  let bgcolor = ''
                  if (row.is_holiday) {
                    bgcolor = 'red'
                  } 

                  return data ? `<span style="color: ${bgcolor}">${data}</span>` : ''
                }
              },
              { 
                data: 'SCHNAME',
                render: function(data, type, row) {
                  let bgcolor = ''
                  if (row.is_holiday) {
                    bgcolor = 'red'
                  } 
                  return data ? `<span style="color: ${bgcolor}">${data}</span>` : ''
                }
              },
              { 
                data: 'STARTTIME',
                render: function(data, type, row) {
                  let bgcolor = ''
                  if (row.is_holiday) {
                    bgcolor = 'red'
                  } 
                  return data ? `<span style="color: ${bgcolor}">${data}</span>` : ''
                }
              },
              { 
                data: 'ENDTIME',
                render: function(data, type, row) {
                  let bgcolor = ''
                  if (row.is_holiday) {
                    bgcolor = 'red'
                  } 
                  return data ? `<span style="color: ${bgcolor}">${data}</span>` : ''
                }
              },
              { 
                data: 'interval_work',
                render: function(data, type, row) {
                  let bgcolor = ''
                  if (row.is_holiday) {
                    bgcolor = 'red'
                  } 
                  return data ? `<span style="color: ${bgcolor}">${data}</span>` : ''
                }
              },
              { 
                data: 'employee_checkin_time',
                render: function(data, type, row) {
                  let bgcolor = ''
                  if (row.is_holiday) {
                    bgcolor = 'red'
                  } 
                  return data ? `<span style="color: ${bgcolor}">${data}</span>` : ''
                }
              },
              { 
                data: 'employee_checkout_time',
                render: function(data, type, row) {
                  let bgcolor = ''
                  if (row.is_holiday) {
                    bgcolor = 'red'
                  } 
                  return data ? `<span style="color: ${bgcolor}">${data}</span>` : ''
                } 
              },
              { 
                data: 'interval_checkinout',
                render: function(data, type, row) {
                  let bgcolor = ''
                  if (row.is_holiday) {
                    bgcolor = 'red'
                  } 
                  return data ? `<span style="color: ${bgcolor}">${data}</span>` : ''
                } 
              },
              { 
                data: 'late_time',
                render: function(data, type, row) {
                  let bgcolor = ''
                  if (row.is_holiday) {
                    bgcolor = 'red'
                  } 
                  return data ? `<span style="color: ${bgcolor}">${data}</span>` : ''
                } 
              },
              { 
                data: 'home_early',
                render: function(data, type, row) {
                  let bgcolor = ''
                  if (row.is_holiday) {
                    bgcolor = 'red'
                  } 
                  return data ? `<span style="color: ${bgcolor}">${data}</span>` : ''
                } 
              },
              {
                data: 'empty_data',
                render: function(data, type, row) {
                  if (row.is_holiday) {
                    return ''
                  } else {
                    
                    return !data ? '' : '<i class="fa fa-check" style="color: red"></i>'
                  }
                },
                className: 'text-center'
              },
              { 
                render: function(data, type, row) {
                  if (row.is_holiday) {
                    return `<span style="color: red">${row.holiday_name}</span>`
                  } else {
                    return ''
                  }
                } 
              },
            ]
          })
        }

        $('#btn-search').on('click', function() {
          let user_id = $('#user-id').val()
          let daterange = $('#daterange').val()
          let splitdate = daterange.split('-')
          let start_date = moment(splitdate[0]).format('YYYY-MM-DD')
          let end_date = moment(splitdate[1]).format('YYYY-MM-DD')

          list_data(user_id.toString(), start_date, end_date)

        })
      })
    </script>

    <!-- Option 2: Separate Popper and Bootstrap JS -->
    <!--
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js" integrity="sha384-7+zCNj/IqJ95wo16oMtfsKbZ9ccEh31eOz1HGyDuCQ6wgnyJNSYdrPa03rtR1zdB" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js" integrity="sha384-QJHtvGhmr9XOIpI6YVutG+2QOK9T+ZnN4kzFN1RtK3zEFEIsxhlmWl5/YESvpZ13" crossorigin="anonymous"></script>
    -->
  </body>
</html>