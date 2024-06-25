<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

    <style>
        .fc-col-header-cell-cushion, .fc-list-day-text, .fc-daygrid-day-number {
            text-decoration: none;
            color: #2c3e50;
        }
    </style>

    <title>Full Calendar!</title>
  </head>
  <body>
    <!-- <h1>Hello, world!</h1> -->

    <div class="container mt-5">
        <div id="calendar"></div>
    </div>


    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.14/index.global.min.js'></script>
    <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/multimonth@6.1.14/index.global.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment@2.30.1/moment.min.js"></script>

    <script>
        let CampaignManage = (DataEvent) => {
            console.log('masuk ke campaign manage: ', DataEvent);
            // document.addEventListener('DOMContentLoaded', function() {
            //     console.log('step 1');
                var calendarEl = document.getElementById('calendar');
                console.log('step 2');
                let calendar = new FullCalendar.Calendar(calendarEl, {
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'multiMonthYear,dayGridMonth,timeGridWeek,timeGridDay,list'  // dayGridMonth,timeGridWeek,listWeek
                    },
                    // initialView: 'dayGridMonth',
                    initialDate: '2017-06-01',
                    // eventDidMount: function(info) {
                    //     $(info.el).tooltip({ 
                    //         title: info.event._def.title,
                    //         placement: "top",
                    //         trigger: "hover",
                    //         container: "body"
                    //     });
                    // },
                    navLinks: true, // can click day/week names to navigate views
                    businessHours: true, // display business hours
                    editable: true,
                    selectable: false,
                    eventTimeFormat: { // like '14:30:00'
                        hour: '2-digit',
                        minute: '2-digit',
                        // second: '2-digit',
                        meridiem: false,
                        hour12: false
                    },
                    events: DataEvent
                });
                console.log('sampe ke atas render');
                calendar.render();
            // });
        }

        // let data = [
        //     {
        //         title: 'All Day Event',
        //         start: '2017-06-01'
        //     },
        //     {
        //         title: 'Long Event',
        //         start: '2017-06-07',
        //         end: '2017-06-10'
        //     },
        //     {
        //         groupId: '999',
        //         title: 'Repeating Event',
        //         start: '2017-06-09T16:00:00'
        //     },
        //     {
        //         groupId: '999',
        //         title: 'Repeating Event',
        //         start: '2017-06-16T16:00:00'
        //     },
        //     {
        //         title: 'Conference',
        //         start: '2017-06-11',
        //         end: '2017-06-13',
        //         color: '#EB1D36'
        //     },
        //     {
        //         title: 'Meeting',
        //         start: '2017-06-12T18:30:00',
        //         end: '2017-06-12T20:30:00',
        //     },
        //     {
        //         title: 'Event Test',
        //         start: '2017-06-12T20:30:00',
        //         end: '2017-06-12T22:30:00',
        //         color: '#EB1D36'
        //     },
        //     {
        //         title: 'Lunch',
        //         start: '2017-06-12T12:00:00'
        //     },
        //     {
        //         title: 'Meeting',
        //         start: '2017-06-12T14:30:00'
        //     },
        //     {
        //         title: 'Birthday Party',
        //         start: '2017-06-13T07:00:00'
        //     },
        //     {
        //         title: 'Click for Google',
        //         url: 'http://google.com/',
        //         start: '2017-06-28'
        //     }
        // ]

        // CampaignManage(data)

        const getData = () => {
            $.ajax({
                'type': 'GET',
                'url': 'http://localhost/InterActive/simple-att/welcome/dayoffapi',
                'data': {
                    'year': '2017'
                }
            }).then(function(res) {

                // Convert date string to Date object for sorting
                const parseDate = (dateStr) => new Date(dateStr.split('-').map((part, index) => index === 1 && part.length === 1 ? `0${part}` : part).join('-'));

                const groupedData = res.data.reduce((acc, curr) => {
                    if (!acc[curr.keterangan]) {
                        acc[curr.keterangan] = [];
                    }
                    acc[curr.keterangan].push(curr.tanggal);
                    return acc;
                }, {});
                
                const result = Object.keys(groupedData).map(key => {
                    const dates = groupedData[key].map(parseDate).sort((a, b) => a - b);
                    const startDate = dates[0].toISOString().split('T')[0];
                    const endDate = dates[dates.length - 1].toISOString().split('T')[0];
                    return {
                        keterangan: key,
                        startDate: startDate,
                        endDate: endDate
                    };
                });

                let sch_data = []

                $.each(result, function(key, val) {
                    let edate = moment(val.endDate)
                        edate = edate.add(1, 'days')
                    
                    sch_data.push({
                        title: val.keterangan,
                        start: val.startDate,
                        end: edate.format('YYYY-MM-DD'),
                        color: '#EB1D36'
                    })
                })
                console.log('ajax response: ');

                // $(document).ready(function() {
                    CampaignManage(sch_data);
                // });
            }).catch(function(err) {
                console.log('ajax error: ', err);
            })
        }

        getData()

        // $(document).ready(function() {
        // });
    </script>

  </body>
</html>