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

    <script>
        // $(document).ready(function() {
            let CampaignManage = (DataEvent) => {
                document.addEventListener('DOMContentLoaded', function() {
                    var calendarEl = document.getElementById('calendar');
                
                    let calendar = new FullCalendar.Calendar(calendarEl, {
                        headerToolbar: {
                            left: 'prev,next today',
                            center: 'title',
                            right: 'multiMonthYear,dayGridMonth,timeGridWeek,timeGridDay,list'  // dayGridMonth,timeGridWeek,listWeek
                        },
                        // initialView: 'dayGridMonth',
                        initialDate: '2024-06-01',
                        eventDidMount: function(info) {
                            $(info.el).tooltip({ 
                                title: info.event._def.title,
                                placement: "top",
                                trigger: "hover",
                                container: "body"
                            });
                        },
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
                
                    calendar.render();
                });
            }

            let data = [
                {
                    title: 'All Day Event',
                    start: '2024-06-01'
                },
                {
                    title: 'Long Event',
                    start: '2024-06-07',
                    end: '2024-06-10'
                },
                {
                    groupId: '999',
                    title: 'Repeating Event',
                    start: '2024-06-09T16:00:00'
                },
                {
                    groupId: '999',
                    title: 'Repeating Event',
                    start: '2024-06-16T16:00:00'
                },
                {
                    title: 'Conference',
                    start: '2024-06-11',
                    end: '2024-06-13',
                    color: '#EB1D36'
                },
                {
                    title: 'Meeting',
                    start: '2024-06-12T18:30:00',
                    end: '2024-06-12T20:30:00',
                },
                {
                    title: 'Event Test',
                    start: '2024-06-12T20:30:00',
                    end: '2024-06-12T22:30:00',
                    color: '#EB1D36'
                },
                {
                    title: 'Lunch',
                    start: '2024-06-12T12:00:00'
                },
                {
                    title: 'Meeting',
                    start: '2024-06-12T14:30:00'
                },
                {
                    title: 'Birthday Party',
                    start: '2024-06-13T07:00:00'
                },
                {
                    title: 'Click for Google',
                    url: 'http://google.com/',
                    start: '2024-06-28'
                }
            ]

            CampaignManage(data)


        // })
    </script>


  </body>
</html>