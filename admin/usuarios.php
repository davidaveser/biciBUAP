<!DOCTYPE html>
<html lang="es">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>CABI</title>
  
  <!-- GOOGLE -->
  <meta name="google-signin-client_id" content="669947592480-5f5luj26v4tp1bg3tlmcu9oorm38vcvd.apps.googleusercontent.com">
  <meta name="google-signin-cookiepolicy" content="single_host_origin">
  <meta name="google-signin-scope" content="profile email">
  <!-- END GOOGLE -->

  <!-- Script JQUERY -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

  <!-- Material Design Theming -->
  <link rel="stylesheet" href="https://code.getmdl.io/1.1.3/material.orange-indigo.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
  <script src="https://code.getmdl.io/1.1.3/material.min.js"></script>

  <!-- Diseño bootstrap -->
  <!--<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">-->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  <!--<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>-->

  <!-- Google Sign In --> <!--Requerido para iniciar y cerrar sesion-->
  <script src="https://apis.google.com/js/platform.js" defer async></script>

  <!-- Import and configure the Firebase SDK -->
  <!-- These scripts are made available when the app is served or deployed on Firebase Hosting -->
  <!-- If you do not serve/host your project using Firebase Hosting see https://firebase.google.com/docs/web/setup -->
  <script src="https://www.gstatic.com/firebasejs/5.4.0/firebase-app.js"></script>
  <script src="https://www.gstatic.com/firebasejs/5.4.0/firebase-auth.js"></script>
  <script src="https://www.gstatic.com/firebasejs/5.4.0/firebase-database.js"></script>
  <script>
  
	/*
	
	Tipos de cuenta:
	"usuario", "dasu","admin", "visitante"
	
	Los objetos de la BD son
	
	var objetoUsuario={
		email: "correo@juan.com", //variable
		nombre: "Juan"
		aP: "Perez",
		aM: "Lopez",
		tipoCuenta: "usuario"
	};
	
	var objetoBicicleta={
		id: "AKSDAJS", //QR
		marca: "Bennotto"
		color: "Azul",
		rodada: "26",
		email: "correo@juan.com"
	};
	
	var objetoBitacora={
		id: "Adsasa", //variable
		idBici: "AKSDAJS"
		email: "correo@juan.com",
		fechaEntrada: "26/10/09 15:35:23",
		fechaSalida: "26/10/09 18:30:13"
		lugarEntrada: "A7"
		lugarSalida: "A1"
	};	
	*/
	
  
  
      // Initialize Firebase
      var config = {
		apiKey: "AIzaSyD1ukXpdQqBXv97Jus4V3F1jHTnSIQ9JHc",
		authDomain: "cabi-297ee.firebaseapp.com",
		databaseURL: "https://cabi-297ee.firebaseio.com",
		projectId: "cabi-297ee",
		storageBucket: "cabi-297ee.appspot.com",
		messagingSenderId: "669947592480"
	  };
      firebase.initializeApp(config);
	  
	  var secondaryApp = firebase.initializeApp(config, "Secondary");

	  function hayCamposVacios(){
		var correo = document.getElementById('inputEmail').value;
		var pass = document.getElementById('inputPass').value;					
		var nombreUsuario = document.getElementById("inputNombre").value;
		var apUsuario = document.getElementById("inputAP").value;
		var amUsuario = document.getElementById("inputAM").value;
		return correo == "" || pass == "" || nombreUsuario == "" || apUsuario == "" || amUsuario == "";
	  }
	  
      function registroPorCorreo() {
		var correo = document.getElementById('inputEmail').value;
		var pass = document.getElementById('inputPass').value;		
		
		if(!hayCamposVacios()){		
			secondaryApp.auth().createUserWithEmailAndPassword(correo,pass).then(function(credential){
				
				//Negamos que se inicie la sesion de esta cuenta creada
				secondaryApp.auth().signOut();
				
				//console.log("El UID es: "+credential.user.uid);
				//Los campos restantes deberan obtenerse mediante inputs obligatorios para registrar usuarios
				var uidUser = credential.user.uid;
				
				var seleccion = document.getElementById("inputTipoCuenta");
				var tipoCuenta = seleccion.options[seleccion.selectedIndex].text;
				
				var nombreUsuario = document.getElementById("inputNombre").value;
				var apUsuario = document.getElementById("inputAP").value;
				var amUsuario = document.getElementById("inputAM").value;
				
				var objetoUsuario = {
					email: correo, //variable
					nombre: nombreUsuario,
					aP: apUsuario,
					aM: amUsuario,
					tipoCuenta: tipoCuenta
				};				
				
				firebase.database().ref('/usuarios/'+uidUser).set(objetoUsuario).then(function(mensaje){
					alert('Usuario registrado correctamente');
				},function(error){
					alert('Ha sucedido un error con la petición');
				});
			},function(error) {
				var errorCode = error.code;
				var errorMessage = error.message;
				console.log(errorCode);			
				
				switch(errorCode){
					case "auth/email-already-in-use":
						alert("El email ingresado ya está en uso");
					break;
					case "auth/invalid-email":
						alert("Ingresa un email válido");
					break;
					case "auth/operation-not-allowed":
						alert("Operación no autorizada");
					break;
					case "auth/weak-password":
						alert("Contraseña débil. Intenta ingresando otra.");
					break;
					default:
						alert("Se ha producido un error. Intenta más tarde.");
					break;			
				}				
			  });
			  
		}else{
			alert("Todos los campos son obligatorios");
		}      
    }
	
	function getPerfilUsuario(){
		//Obtenemos el uid del usuario que está ya loggeado
		var userId = firebase.auth().currentUser.uid;
		//Se crea una referencia a la rama /usuarios
		var perfilRef = firebase.database().ref('/usuarios/'+userId);
		  
		//Se hace consulta por valor (cuando se quieren obtener todos)
		//perfilRef.orderByValue().on('value', function(snapshot) {
		//Esta es una busqueda específica, busca dentro de los objetos por nombre al que sea igual a Herbert
		//perfilRef.orderByChild("nombre").equalTo("Herbert").on('value', function(snapshot) { //on permite que el campo se actualice al instante sin necesidad de recargar
			//Consulta porque sabemos su uid (en la referencia) 
			perfilRef.orderByValue().on('value', function(snapshot) {
			//Se valida que exista nuestra consulta
			  if (snapshot.exists()){
				console.log('Busqueda ENCONTRADA');
				//Cuando solo tenemos un solo valor porque accedimos con su UID
					var objetoPerfil = snapshot.val();
					actualizarPerfil(objetoPerfil);

				//Si solo tenemos un valor pero sabemos su UID deberiamos acceder sin forEach
				
				//Cuando se van a mostrar todos los datos
				/*
					//Borrar datos anteriores
				  snapshot.forEach(function(child) {
					//y añadir dato por dato
				  });
				*/
			  }else{
				console.log('Busqueda no encontrada');
			  }
		  });
      }
	  
	function actualizarPerfil(perfil){
		var inputMuestraNombre = document.getElementById("inputMuestraNombre");
		inputMuestraNombre.innerHTML = perfil.nombre; //Se accede como atributo del objeto y se llena
	}
	
	function initApp() {
		// Auth state changes.
		// [START authstatelistener]
		firebase.auth().onAuthStateChanged(function(user){
			if (user) {
				getPerfilUsuario();
			}else{
				//No ha accedido el usuario y no puede acceder a esta página
				$.ajax({ url: '../process/userManagement.php',
					data: {action: 'logout',tipoCuenta: ''},
					type: 'post',
					success:
						function() {
							window.location.replace("http://cabi.dx.am/");
						}
				});
              }
          });
		  
		//Mandamos a llamar la funcion que hace la consulta y carga los datos
		//getPerfilUsuario();
		  
		document.getElementById('btnRegistrarUsuario').addEventListener('click', registroPorCorreo, false);
		document.getElementById('btnCerrarSesion').addEventListener('click', handleSignOut, false);
      }

      window.onload = function() {
		  gapi.load('auth2', function() {
			  gapi.auth2.init();
		  });
          initApp();
      };

  </script>
</head>

<body>
	<h1>Registro</h1>
	<p>Bienvenido <span id="inputMuestraNombre">Nombre<span></p>

	<h2>Se muestran todos los usuarios</h2>
	<input id="inputEmail" type="mail"  placeholder="juan@mail.com"> <br>
	<input id="inputPass" type="password"  placeholder="*********"> <br>
	<input id="inputNombre" type="mail"  placeholder="nombre"> <br>
	<input id="inputAP" type="text" placeholder="ap"> <br>
	<input id="inputAM" type="text" placeholder="am"> <br>
	<select id="inputTipoCuenta" name="inputTipoCuenta">
		<option value="usuario">usuario</option>
		<option value="visitante">visitante</option>
		<option value="dasu">dasu</option>
		<option value="admin">admin</option>
	</select> <br>
			
	<input id="inputRodada" type="number"  placeholder="Rodada"> <br>
	
	<button id="btnRegistrarUsuario">Registrar usuario</button> <br>	
	
	<button id="btnCerrarSesion">Cerrar sesión</button> 	
	
	
	
  <!-- End custom js for this page-->
  <script type="text/javascript">
      function handleSignOut() {
          var googleAuth = gapi.auth2.getAuthInstance();
          googleAuth.signOut().then(function() {
              firebase.auth().signOut();
          });
      }
  </script>
</body>

</html>
