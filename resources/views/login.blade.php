<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>IFRAN School</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">

    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Crimson+Pro:wght@700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link rel="stylesheet" href="{{ asset('css/login.css') }}">

    <style>
        .login-container {
            background: rgba(0, 0, 0, 0.5);
            padding: 2.5rem;
            border-radius: 15px;
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            max-width: 400px;
            width: 90%;
            position: relative;
            z-index: 2;
        }

        .login-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.4);
            border-radius: 15px;
            z-index: -1;
        }

        .login-container:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.6);
            border-color: rgba(78, 161, 255, 0.3);
        }

        .login-container h1 {
            color: #fff;
            text-align: center;
            margin-bottom: 2rem;
            font-size: 2.5rem;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }

        .input-group {
            position: relative;
            margin-bottom: 1.5rem;
        }

        .input-group input {
            width: 100%;
            padding: 10px;
            border: none;
            border-bottom: 2px solid rgba(255, 255, 255, 0.2);
            background: transparent;
            color: #fff;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .input-group label {
            position: absolute;
            left: 0;
            top: 10px;
            color: rgba(255, 255, 255, 0.7);
            transition: all 0.3s ease;
            pointer-events: none;
        }

        .input-group input:focus,
        .input-group input:valid {
            border-bottom-color: #4ea1ff;
            outline: none;
        }

        .input-group input:focus + label,
        .input-group input:valid + label {
            transform: translateY(-20px);
            font-size: 0.8rem;
            color: #4ea1ff;
        }

        button[type="submit"] {
            width: 100%;
            padding: 12px;
            background: #4ea1ff;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(78, 161, 255, 0.3);
        }

        button[type="submit"]:hover {
            background: #3d8be6;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(78, 161, 255, 0.4);
        }

        .forgot-password {
            text-align: center;
            margin-top: 1rem;
        }

        .forgot-password a {
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            font-size: 0.9rem;
            transition: color 0.3s ease;
        }

        .forgot-password a:hover {
            color: #4ea1ff;
        }

        .alert {
            background: rgba(220, 53, 69, 0.9);
            color: white;
            padding: 1rem;
            border-radius: 5px;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3);
        }

        .alert ul {
            margin: 0;
            padding-left: 1.5rem;
        }

        .alert li {
            margin-bottom: 0.5rem;
        }

        #particles-js {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            z-index: 1;
        }

        .login-container {
            position: relative;
            z-index: 2;
        }
    </style>

</head>

<body>
    <!-- particles.js container -->
    <div id="particles-js"></div>

    <div class="login-container">

        <h1><i class="fas fa-graduation-cap"></i> IFRAN</h1>

        <form method="POST" action="{{ route('login') }}">

        @csrf

            @if ($errors->any())

                <div class="alert alert-danger">

                    <ul>

                        @foreach ($errors->all() as $error)

                            <li>{{ $error }}</li>

                        @endforeach

                    </ul>

                </div>

            @endif

            <div class="input-group">

                <input type="email" id="email" name="email" required>

                <label for="email">Email address</label>

            </div>

            <div class="input-group">

                <input type="password" id="password" name="password" required>

                <label for="password">Password</label>
            </div>
            <button type="submit">Sign In</button>

        </form>
        <div class="forgot-password">
            <a href="#">Forgot password?</a>
        </div>
    </div>

    <!-- particles.js lib -->
    <script src="http://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
    
    <!-- stats.js lib -->
    <script src="http://threejs.org/examples/js/libs/stats.min.js"></script>

    <script>
        particlesJS("particles-js", {
            particles: {
                number: { value: 80, density: { enable: true, value_area: 1000 } },
                color: { value: ["#4ea1ff", "#3d8be6", "#1a3a6a"] },
                shape: {
                    type: "circle",
                    stroke: { width: 0, color: "#000000" },
                    polygon: { nb_sides: 5 }
                },
                opacity: {
                    value: 0.5,
                    random: true,
                    anim: { enable: true, speed: 1, opacity_min: 0.1, sync: false }
                },
                size: {
                    value: 3,
                    random: true,
                    anim: { enable: true, speed: 2, size_min: 0.1, sync: false }
                },
                line_linked: {
                    enable: true,
                    distance: 150,
                    color: "#4ea1ff",
                    opacity: 0.3,
                    width: 1
                },
                move: {
                    enable: true,
                    speed: 2,
                    direction: "none",
                    random: false,
                    straight: false,
                    out_mode: "out",
                    bounce: false,
                    attract: { enable: true, rotateX: 600, rotateY: 1200 }
                }
            },
            interactivity: {
                detect_on: "canvas",
                events: {
                    onhover: { enable: true, mode: "grab" },
                    onclick: { enable: true, mode: "push" },
                    resize: true
                },
                modes: {
                    grab: { distance: 140, line_linked: { opacity: 0.8 } },
                    bubble: { distance: 400, size: 40, duration: 2, opacity: 8, speed: 3 },
                    repulse: { distance: 200, duration: 0.4 },
                    push: { particles_nb: 4 },
                    remove: { particles_nb: 2 }
                }
            },
            retina_detect: true
        });

        var count_particles, stats, update;
        stats = new Stats();
        stats.setMode(0);
        stats.domElement.style.position = "absolute";
        stats.domElement.style.left = "0px";
        stats.domElement.style.top = "0px";
        document.body.appendChild(stats.domElement);
        count_particles = document.querySelector(".js-count-particles");
        update = function () {
            stats.begin();
            stats.end();
            if (window.pJSDom[0].pJS.particles && window.pJSDom[0].pJS.particles.array) {
                count_particles.innerText = window.pJSDom[0].pJS.particles.array.length;
            }
            requestAnimationFrame(update);
        };
        requestAnimationFrame(update);
    </script>
</body>
</html>
