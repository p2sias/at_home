@extends('layout.app')

@section('custom-css')
    <link rel="stylesheet" href="{{URL::asset('css/register.css')}}">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
@endsection

@section('custom-js')
    <script src="{{URL::asset('js/error-message.js')}}"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
@endsection

@section('content')
    <div class="errors-box hidden">
        <span id="errors-quit">X</span>
        <ul class="errors-list">
            @if ($errors->any())
                @foreach ($errors->all() as $error)
                    <li class="error">{{ $error }}</li>
                @endforeach
            @endif
        </ul>
    </div>
    <div id="register-box">
        <div class="register-form-box">
            <form id="myForm" action="/register" method="POST">
                @csrf
                <div id="name-inputs">
                    <div class="input-box">
                        <span class="label name">Prénom</span>
                        <input class="input name" type="text" name="name" placeholder="Pierre" />
                    </div>
                    <div class="input-box">
                        <span class="label surname">Nom</span>
                        <input class="input surname" type="text" name="surname" placeholder="Dupont" />
                    </div>
                    <div class="input-box">
                        <span class="label pseudo">Pseudo</span>
                        <input class="input pseudo" type="text" name="pseudo" />
                    </div>
                </div>
                <div class="input-box">
                    <span class="label birth">Date de naissance</span>
                    <input class="input birth" type="date" name="birthday" max="{{date('Y-m-d', strtotime(now() . "-18 year"))}}"/>
                </div>
                <div id="contact-login-inputs">
                    <div id="contact-inputs">
                        <div class="input-box">
                            <span class="label email">Email</span>
                            <input class="input email" type="email" name="email" placeholder="exemple@exemple.fr" />
                        </div>
                        <div class="input-box">
                            <span class="label phone">Téléphone</span>
                            <input class="input phone" type="text" name="phone" placeholder="0612345678" />
                        </div>
                    </div>
                    <div id="password-inputs">
                        <div class="input-box">
                            <span class="label password">Mot de passe</span>
                            <input class="input password" type="password" name="password" />
                        </div>
                        <div class="input-box">
                            <span class="label pass-verif">Vérification de mot de passe</span>
                            <input class="input pass-verif" type="password" />
                        </div>
                    </div>
                </div>
                <div id="location-inputs">
                    <div class="input-box">
                        <span class="label adress">Adresse</span>
                        <input class="input adress" type="text" name="address" />
                    </div>
                    <div class="input-box">
                        <span class="label city">Ville</span>
                        <input class="input city" type="text" name="city" />
                    </div>
                    <div class="input-box">
                        <span class="label pcode">Code postal</span>
                        <input class="input pcode" type="text" name="postal_code" />
                    </div>
                </div>
                <input class="btn next" type="submit" value="Suivant" />
            </form>

        </div>
        <div id="resume-box">
            <p class="resume-text">
                Bonjour ! <br><br>
                Je m'appelle <span class="resume-field name">Pierre</span>
                <span class="resume-field surname">Dupont</span> mais on me connait sous le pseudonyme <span class="resume-field pseudo">PierreDupont18</span>  , et je suis né le <span class="resume-field birth">xx/xx/xxxx</span> <br>
                J'aimerai me lancer dans l'aventure At Home A Game afin de découvrir les joies de relever des défis sans bouger de chez moi ! <br><br>
                En parlant de chez moi,
                j'habite au <span class="resume-field adress">2 rue des Dupont</span> dans un charmant petit endroit appelé <span class="resume-field city">Paris</span>, <span class="resume-field pcode">95800</span> <br>
                Voici mes informations de contacts : <br>
            </p>
                <ul class='resume-text'>
                    <li>Mon adress email est <span class="resume-field email">exemple@exemple.fr</span></li>
                    <li>Mon numéro de téléphone est <span class="resume-field phone">0612345678</span></li>
                </ul> <br> <br>
            <p class="resume-text">
                Cordialement,<br>
                <span class="resume-field name">Pierre</span> <span class="resume-field suname">Dupont</span>
            </p>
        </div>
    </div>
@endsection
