var tabela = document.getElementById("apontamento");
var linhas = tabela.getElementsByTagName("tr");

for(var i = 0; i < linhas.length; i++){
	var linha = linhas[i];
  linha.addEventListener("click", function(){
  	//Adicionar ao atual
		selLinha(this, false); //Selecione apenas um
    //selLinha(this, true); //Selecione quantos quiser
	});
}

/**
Caso passe true, você pode selecionar multiplas linhas.
Caso passe false, você só pode selecionar uma linha por vez.
**/
function selLinha(linha, multiplos){
	if(!multiplos){
  	var linhas = linha.parentElement.getElementsByTagName("tr");
    for(var i = 0; i < linhas.length; i++){
      var linha_ = linhas[i];
      linha_.classList.remove("selecionado");    
    }
  }
  linha.classList.toggle("selecionado");
}

/**
Exemplo de como capturar os dados
**/
var btnVisualizar = document.getElementById("editar");


btnVisualizar.addEventListener("click", function(){
	var selecionados = tabela.getElementsByClassName("selecionado");
  //Verificar se eestá selecionado
  if(selecionados.length < 1){
  	alert("Selecione pelo menos uma linha");
    return false;
  }
  
  var dados = "";
  
  for(var i = 0; i < selecionados.length; i++){
  	var selecionado = selecionados[i];
    selecionado = selecionado.getElementsByTagName("td");
    dados += "ID: " + selecionado[0].innerHTML + " - Nome: " + selecionado[1].innerHTML + " - Idade: " + selecionado[2].innerHTML + "\n";
  }
  var id = selecionado['0'].innerHTML;
  var usr_id = selecionado['1'].innerHTML;
  var prdnme = selecionado['2'].innerHTML;
  var oprnme = selecionado['3'].innerHTML;
  var ctynme = selecionado['4'].innerHTML;
  var usrask = selecionado['5'].innerHTML;
  var adddte = selecionado['6'].innerHTML;
  var fr_tim = selecionado['7'].innerHTML;
  var to_tim = selecionado['8'].innerHTML;
  var usrobs = selecionado['9'].innerHTML;

  jQuery.ajax({
    type: "GET",
    url: "ver.php",
    dataType: "html",
    data: {
        id: id, 
        usr_id: usr_id,
        prdnme: prdnme, 
        oprnme: oprnme,
        ctynme: ctynme , 
        usrask: usrask,
        adddte: adddte,
        fr_tim: fr_tim,
        to_tim: to_tim,
        usrobs: usrobs
    },
// enviado com sucesso
    success: function(response){
      jQuery(".selecionado").empty();
      jQuery(".selecionado").append(response);
    },
    // quando houver erro
    error: function(){
        alert("Erro no Ajax");
    }
});

});