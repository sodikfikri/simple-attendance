<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

    <title>Tree!</title>
  </head>
  <body>
    <!-- <h1>Hello, world!</h1> -->

    <div class="container mt-5">
        <div id="svg-tree"></div>
    </div>


    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdn.jsdelivr.net/npm/apextree"></script>

    <script>
        $(document).ready(function() {
            const data = {
                "id": "idA",
                "name": "RnD <br> Programmer",
                "options": {
                    "nodeBGColor": '#00afb9',
                },
                "children": [
                    {
                        "id": "idB",
                        "name": "LEADER <br> Iwan Sasmiko",
                        "options": {
                            "nodeBGColor": '#84a59d',
                        },
                        "children": [
                            {
                                "id": "idC",
                                "name": "Moh Sodik Fikri",
                            },
                            {
                                "id": "idD",
                                "name": "Fais Albaya",
                            },
                            {
                                "id": "idD",
                                "name": "Alfin Fatoni",
                            },
                            {
                                "id": "idD",
                                "name": "Pasya Ibrahim",
                            },
                        ]
                    },
                    {
                        "id": "idB",
                        "name": "LEADER <br> Iwan Sasmiko",
                        "options": {
                            "nodeBGColor": '#84a59d',
                        },
                        "children": [
                            {
                                "id": "idC",
                                "name": "Moh Sodik Fikri",
                            },
                            {
                                "id": "idD",
                                "name": "Fais Albaya",
                            },
                            {
                                "id": "idD",
                                "name": "Alfin Fatoni",
                            },
                            {
                                "id": "idD",
                                "name": "Pasya Ibrahim",
                            },
                        ]
                    },
                    {
                        "id": "idB",
                        "name": "LEADER <br> Iwan Sasmiko",
                        "options": {
                            "nodeBGColor": '#84a59d',
                        },
                        "children": [
                            {
                                "id": "idC",
                                "name": "Moh Sodik Fikri",
                            },
                            {
                                "id": "idD",
                                "name": "Fais Albaya",
                            },
                            {
                                "id": "idD",
                                "name": "Alfin Fatoni",
                            },
                            {
                                "id": "idD",
                                "name": "Pasya Ibrahim",
                            },
                        ]
                    },
                ]
            };

            // const options = {
            //     width: 1100,
            //     height: 700,
            //     nodeWidth: 120,
            //     nodeHeight: 80,
            //     childrenSpacing: 100,
            //     siblingSpacing: 30,
            //     direction: 'top',
            //     canvasStyle: 'border: 1px solid black; background: #fff;',
            //     enableToolbar: true,
            // };

            $.ajax({
                url: 'http://localhost/simple-attendance/welcome/departements',
                method: 'GET'
            }).then((res) => {
                const options = {
                    width: 1100,
                    height: 700,
                    nodeWidth: 120,
                    nodeHeight: 80,
                    childrenSpacing: 100,
                    siblingSpacing: 30,
                    direction: 'top',
                    canvasStyle: 'background: #F2FAFD;',
                    enableToolbar: true,
                };
                console.log('response: ', res);
                const tree = new ApexTree(document.getElementById('svg-tree'), options);
                const graph = tree.render(res.data[0]);
            })

        })
    </script>


  </body>
</html>