  <!-- Page Content -->
    <div class="page-heading contact-heading header-text">
      <div class="container">
        <div class="row">
          <div class="col-md-12">
            <div class="text-content">
              <h4 class=" wow fadeInDown" data-wow-delay="0.8s" data-wow-duration="1.2s">NOUS CONTACTER</h4>
              <h2 class=" wow fadeInDown" data-wow-delay="0.6s" data-wow-duration="1.2s">AU PLAISIR</h2>
            </div>
          </div>
        </div>
      </div>
	 </div>
	<!-- Page Content End -->
    <!-- Container -->
    <div class="send-message">
      <div class="container">
        <div class="row">
          <div class="col-md-12">
            <div class="section-heading">
            <h2 class=" wow fadeInUp" data-wow-delay="0.8s" data-wow-duration="1.2s">Envoyez un message</h2>
            </div>
          </div>
          <div class="col-md-8">
          <div class="contact-form">
              <form id="contact" action=""  method="post">
                <?php
                  if ($_SERVER["REQUEST_METHOD"] == "POST") {
                    // Récupérer les valeurs des champs du formulaire
                    $nom = $_POST["Nom"];
                    $email = $_POST["email"];
                    $sujet = $_POST["sujet"];
                    $message = $_POST["message"];

                    // Adresse e-mail de destination
                    $destinataire = "contact@paroisseuniversitairestespritlushi.com";

                    // Sujet du message
                    $sujetEmail = "Nouveau message depuis le formulaire de contact";

                    // Corps du message
                    $corpsMessage = "Nom : $nom\n";
                    $corpsMessage .= "E-mail : $email\n";
                    $corpsMessage .= "Sujet : $sujet\n";
                    $corpsMessage .= "Message :\n$message";

                    // Envoi de l'e-mail
                    if (mail($destinataire, $sujetEmail, $corpsMessage)) {
                      echo '<span style="color: green;">Votre message a été envoyé avec succès !</span>';
                    } else {
                      echo '<span style="color: red;">Une erreur s\'est produite lors de l\'envoi du message. Veuillez réessayer plus tard.</span>';
                    }
                  }
                ?>

                <div class="row">
                  <div class="col-lg-12 col-md-12 col-sm-12">
                    <fieldset class=" wow fadeInLeft" data-wow-delay="0.8s" data-wow-duration="1s">
                      <input name="Nom"  type="text" class="form-control" id="name" placeholder="Nom complet" required>
                    </fieldset>
                  </div>
                  <div class="col-lg-12 col-md-12 col-sm-12">
                    <fieldset class=" wow fadeInLeft" data-wow-delay="0.8s" data-wow-duration="1.1s">
                      <input name="email" type="email" class="form-control" id="email" placeholder="Adresse mail" required>
                    </fieldset>
                </div>
                  <div class="col-lg-12 col-md-12 col-sm-12">
                    <fieldset class=" wow fadeInLeft" data-wow-delay="0.8s" data-wow-duration="1.3s">
                      <input name="sujet" type="text" class="form-control" id="subject" placeholder="Sujet" required>
                    </fieldset>
                  </div>
                  <div class="col-lg-12">
                    <fieldset class=" wow fadeInLeft" data-wow-delay="0.8s" data-wow-duration="1.4s">
                      <textarea name="message" rows="6" class="form-control" id="message" placeholder="Votre message" required></textarea>
                    </fieldset>
                  </div>
                  <div class="col-lg-12">
                    <fieldset class=" wow fadeInLeft" data-wow-delay="0.8s" data-wow-duration="1.5s">
                      <button name ="send" type="submit" id="form-submit" class=" filled-button"> Envoyer le message</button>
                    </fieldset>
                  </div>
                </div>
              </form>
            </div>
          </div>
          
          <div class="col-md-4">
            <ul class="accordion">
              <li class=" wow fadeInRight" data-wow-delay="0.8s" data-wow-duration="1s">
                  <a>Numéros de contact</a>
                  <div class="content">
                      <p>+243 99 046 6905</p>
                  </div>
              </li>
              <li class=" wow fadeInRight" data-wow-delay="0.8s" data-wow-duration="1.1s">
                  <a>Adresse E-mail</a>
                  <div class="content">
                      <p>contact@paroisseuniversitairestespritlushi.com</p>
                  </div>
              </li>
              <li class=" wow fadeInRight" data-wow-delay="0.8s" data-wow-duration="1.3s">
                  <a>Notre adresse</a>
                  <div class="content">
                      <p>Route Kasapa, en face de la pleine TSHOMBE</p>
                  </div>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
   <!-- Container End -->
  <div class="authent wow fadeInRight" data-wow-delay="0.8s" data-wow-duration="1.3s">
        <p>Vous êtes prié de vous authentifier via ce formulaire pour plus d'informations sur vous.</p>
  </div>

<section class="login  wow fadeInDown" data-wow-delay="0.8s" data-wow-duration="1.2s">
  <form action="assets/pages/formulaire.php" method="GET">
      <h3>s'authentifier</h3>
      <input type="text" name="nom" placeholder="Votre nom complet *obligatoire*" class="box" required>
      <input type="email" name="mail" placeholder="Votre E-mail *obligatoire*" class="box" required>
      <input type="text" name="adresse" placeholder="Ville de provenance *obligatoire*" class="box" required>
      <select name="cev" class="box" required>
        <option>Choisir une C.E.V d'appartenance</option>
        <option name="cev">Cev Anaurite</option>
        <option name="cev">Cev Bakanja</option>
        <option name="cev">Cev Bakhita</option>
        <option name="cev">Cev Bethanie</option>
        <option name="cev">Cev Coeur Im.</option>
        <option name="cev">Cev St Charles L.</option>
        <option name="cev">Cev St Ignace</option>
        <option name="cev">Cev St Kizito</option>
        <option name="cev">Cev St Vincent de Paul</option>
        <option name="cev">Cev St Therese</option>
      </select>
      <input type="phone" name="phone" placeholder="Votre numéro" class="box" required>
      <input type="submit" name="submit"  class="btn" value="Valider"></input>
  </form>
</section>

   
