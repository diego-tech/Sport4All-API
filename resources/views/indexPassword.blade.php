<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <!-- Links -->
    <link rel="icon" href="{{url('img/logoapp.png') }}">

    <style>
        body {
            background-color: #D0E1F9;
            color: #4D648D;
        }

        p {
            color: #4D648D;
            text-align: center;
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
        }

        .formTitle {
            font-weight: 600;
            margin-top: 20px;
            color: #335EA8;
            text-align: center;
        }

        .inputLabel {
            font-size: 12px;
            color: #335EA8;
            margin-bottom: 6px;
            margin-top: 24px;
        }

        .inputDiv {
            width: 70%;
            display: flex;
            flex-direction: column;
            margin: auto;
        }

        input {
            height: 40px;
            font-size: 16px;
            border-radius: 4px;
            border: none;
            border: solid 1px #ccc;
            padding: 0 11px;
        }

        input:disabled {
            cursor: not-allowed;
            border: solid 1px #eee;
        }

        .buttonWrapper {
            margin-top: 40px;
        }

        .submitButton {
            width: 70%;
            height: 40px;
            margin: auto;
            display: block;
            color: #F7F7F7;
            background-color: #335EA8;
            border-color: #335EA8;
            text-shadow: 0 -1px 0 rgba(0, 0, 0, 0.12);
            box-shadow: 0 2px 0 rgba(0, 0, 0, 0.035);
            border-radius: 4px;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .submitButton:hover {
            transform: scale(1.05);
        }

        .submitButton:disabled,
        button[disabled] {
            border: 1px solid #cccccc;
            background-color: #cccccc;
            color: #666666;
        }

        .alert {
            color: red;
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
            <form action="/resetPassword" method="POST" name="signupForm" id="signupForm">
                @csrf
                <img src="{{url('img/logoapp.png')}}" id="signupLogo" />

                <input type="hidden" name="token" value="{{ $token }}">

                <h2 class="formTitle">
                    Restablezca su contrase??a
                </h2>

                <div class="inputDiv">
                    <label class="inputLabel" for="email">Email *</label>
                    <input type="email" id="email" name="email" required>
                </div>

                <div class="inputDiv">
                    <label class="inputLabel" for="password">Nueva contrase??a *</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <div class="inputDiv">
                    <label class="inputLabel" for="password_confirmation">Confirma la nueva contrase??a *</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required>
                </div>

                <div class="buttonWrapper">
                    <button type="submit" id="submitButton" class="submitButton pure-button pure-button-primary">
                        <span>Continuar</span>
                    </button>
                </div>
            </form>

            @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
        </div>
    </div>
</body>

</html>