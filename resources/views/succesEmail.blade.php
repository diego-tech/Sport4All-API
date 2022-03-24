<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <style>

        body {
            background-color: #D0E1F9;
            color: #4D648D;
        }

        .mainDiv {
            display: flex;
            min-height: 100%;
            align-items: center;
            justify-content: center;
            background-color: #D0E1F9;
            font-family: 'Open Sans', sans-serif;
        }

        .cardStyle {
            width: 500px;
            border-color: #4D648D;
            background: #fff;
            padding: 36px 0;
            border-radius: 4px;
            margin: 30px 0;
            box-shadow: 0px 0 30px 0 rgba(0, 0, 0, 0.25);
        }

        #signupLogo {
            max-height: 100px;
            margin: auto;
            display: flex;
            flex-direction: column;
            transition: all 0.3s;
        }

        img:hover {
            transform: scale(1.1);
        }

        .formTitle {
            font-weight: 600;
            margin-top: 20px;
            color: #335EA8;
            text-align: center;
        }

        .infoLabel {
            font-size: 12px;
            color: #335EA8;
            margin-bottom: 6px;
            margin-top: 24px;
        }

        .infoDiv {
            width: 70%;
            display: flex;
            flex-direction: column;
            text-align: center;
            margin: auto;
        }

        

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>
</head>

<body>
    <div class="mainDiv">
        <div class="cardStyle">
            
                <img src="{{url('img/logoapp.png') }}" id="signupLogo" />

                <h2 class="formTitle">
                    Email Verificado
                </h2>

                <div class="infoDiv">
                    <label class="infoLabel">Su email ha sido verificado de manera satisfactoria. <br><br>
                        ¡Ya puede comenzar a utilizar Sport4All!</label>
                </div>

                

            </form>
        </div>
    </div>
</body>

</html>