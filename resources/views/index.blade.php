<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
    <title>Sports4All</title>

    <!-- Links -->
    <link rel="icon" href="{{url('img/logoapp.png') }}">
    <link rel="stylesheet" href="{{url('css/app.css')}}" />
</head>

<body class="is-preload">
    <!-- Header -->
    <div id="header">
        <span class="logo icon"> <img src="{{url('img/logoapp.png')}}" /></span>
        <h1>Bienvenido a Sports4All</h1>
        <p>
            ¡Somos una aplicación preparada para organizar tus partidos!
            <br />
            Automatización y Gestión de Centros Deportivos
        </p>
    </div>

    <!-- Main -->
    <div id="main">
        <header class="major container medium">
            <h2>
                Lo básico es creer en tí
                <br />
                e intentar dar lo mejor
                <br />
                de uno mismo cada día
            </h2>
            <p>"Rafa Nadal"</p>
        </header>

        <div class="box alt container">
            <section class="feature left">
                <a href="" class="image icon solid fa-mobile-alt"><img src="{{url('img/pic01.jpg')}}" alt="" /></a>
                <div class="content">
                    <h3>Reservar es fácil</h3>
                    <p>
                        Reserva tus pistas tanto de pádel como de tenis de una manera
                        sencilla para jugar con amigos o con nuevos contrincantes. <br />
                        ¿Por qué no hacer nuevos amigos?
                    </p>
                </div>
            </section>
            <section class="feature right">
                <a href="" class="image icon solid fa-user-friends"><img src="{{url('img/pic02.jpg')}}" alt="" /></a>
                <div class="content">
                    <h3>Hazte socio de nuestros clubes adscritos</h3>
                    <p>
                        Inscríbete en nuestra selección de clubes para poder adquirir sus
                        ventajas y beneficios. <br />
                        ¡No te pierdas ninguno de sus eventos y ofertas!
                    </p>
                </div>
            </section>
            <section class="feature left">
                <a href="" class="image icon solid fa-qrcode"><img src="{{url('img/pic03.jpg')}}" alt="" /></a>
                <div class="content">
                    <h3>Accede a las pistas de una manera sencilla</h3>
                    <p>
                        Con nuestro sistema integrado de automatización, cuando reserves
                        una pista tendrás un QR disponible para acceder a ella. ¡Hazlo de
                        una manera dencilla!
                    </p>
                </div>
            </section>
        </div>

        <div class="box container">
            <section>
                <header>
                    <h3>Nuestros Clubes</h3>
                </header>
                <div class="table-wrapper">
                    <table class="default">
                        <thead>
                            <tr>
                                <th>Club</th>
                                <th>Descripción</th>
                                <th>Valoración media</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    Club deportivo <br />
                                    Juan Lucas
                                </td>
                                <td>Es el club deportivo de pádel de Juan Lucas</td>
                                <td>7/10</td>
                            </tr>
                            <tr>
                                <td>Asociación deportiva Vallecas</td>
                                <td>
                                    Club deportivo con pistas de pádel y tenis disponibles
                                </td>
                                <td>6.5/10</td>
                            </tr>
                            <tr>
                                <td>Club deportivo Diego Muñoz</td>
                                <td>Dieguiño tiene su propio club de pádel</td>
                                <td>10/10</td>
                            </tr>
                            <tr>
                                <td>Campo de tenis Las Tablas</td>
                                <td>Es un campo de tenis que hay en las tablas</td>
                                <td>4/10</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        <footer class="major container medium">
            <h3>¡Descárgate ya nuestra app!</h3>
            <p>
                Haga click en el botón inferior y da el primer paso para empezar a
                disfrutar del deporte de una manera novedosa
            </p>
            <ul class="actions special">
                <li><a href="#" class="button">¡Descargatela ya!</a></li>
            </ul>
        </footer>
    </div>

    <!-- Footer -->
    <div id="footer">
        <div class="container medium">
            <header class="major last">
                <h2>¿Alguna pregunta?</h2>
            </header>

            <p>
                Si tienes alguna pregunta, comentario o sugerencia no dudes en
                contárnoslo. <br />
                Tu opinión es muy importante para nosotros
            </p>

            <form method="" action="#">
                <div class="row">
                    <div class="col-6 col-12-mobilep">
                        <input type="text" name="name" placeholder="Nombre" />
                    </div>
                    <div class="col-6 col-12-mobilep">
                        <input type="email" name="email" placeholder="Email" />
                    </div>
                    <div class="col-12">
                        <textarea name="message" placeholder="Mensaje" rows="6"></textarea>
                    </div>
                    <div class="col-12">
                        <ul class="actions special">
                            <li><input type="submit" value="Enviar mensaje" /></li>
                        </ul>
                    </div>
                </div>
            </form>
        </div>

        <ul class="copyright">
          <li>&copy; Sports4All.</li>
          <li>Diseñado por: <a href="http://html5up.net">Raccoons S.L.</a></li>
        </ul>
    </div>

    <!-- Js Scripts -->
    <script src="{{url('js/jquery.min.js')}}"></script>
    <script src="{{url('js/browser.min.js')}}"></script>
    <script src="{{url('js/breakpoints.min.js')}}"></script>
    <script src="{{url('js/util.js')}}"></script>
    <script src="{{url('js/main.js')}}"></script>
</body>

</html>