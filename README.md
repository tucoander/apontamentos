<div class="page-header-icon undefined"><span class="icon">💻</span></div>
        <div class="page-body">
            <h1 id="9c9fd3ae-43e2-4d07-baee-bb3b817cb74f" class="">Apontamentos</h1>
            <p id="5abf606d-56d4-4cb0-9076-d31c42872ac0" class="">Essa aplicação foi construída para atender a necessidade de estimar o tempo trabalhado de uma equipe que atende vários localidades com uma série de serviços.</p>
            <p id="78a9b842-9d0b-4bf5-8b4b-a367a09395d9" class="">O objetivo da applicação é que o usuário aponte o tempo trabalhado e especifique qual serviço, dentro de uma listagem de serviços disponíveis, foi realizado para determinada localidade, dentro de uma listagem de localidades possíveis.</p>
            <h2 id="bfb6f804-353e-411a-85c4-884544023583" class="">Tecnologias</h2>
            <p id="5f97dc4f-d2c5-43c6-91df-8db9e2ce63aa" class="">Para criação da ferramenta foram utilizadas as seguintes tecnologias:</p>
            <ul id="b0ea0662-7a1f-43ca-9499-ef48249e7c74" class="bulleted-list">
                <li>PHP 7</li>
            </ul>
            <ul id="d1ad9351-0152-4126-b47c-9b008e247ad8" class="bulleted-list">
                <li>HTML</li>
            </ul>
            <ul id="ed53dec8-2383-4964-a92a-87dcea2ef9c9" class="bulleted-list">
                <li>CSS</li>
            </ul>
            <ul id="cbd9b6c5-ce2a-4cde-9611-7f7a7356835f" class="bulleted-list">
                <li>JavaScript</li>
            </ul>
            <ul id="6974a962-a29d-42e8-987f-4ab461e7d4b0" class="bulleted-list">
                <li>JQuery</li>
            </ul>
            <ul id="a6d9735d-182f-4f75-9936-4ba0f3ed66d4" class="bulleted-list">
                <li>SQLite</li>
            </ul>
            <ul id="5ff238c3-528e-44bb-8f8b-ec081361cbd7" class="bulleted-list">
                <li>Bootstrap</li>
            </ul>
            <h2 id="7a192a46-fb8f-407f-8bc6-ceccf61861ca" class="">Ambiente</h2>
            <p id="9d778aa0-8f1f-4d33-91b2-d9a40aa3d9c5" class="">Para a criação do projeto foi utilizado um servidor portátil <a href="https://www.uwamp.com/en/">Uwamp</a>.</p>
            <h3 id="176af111-dc36-49c6-a659-9172ba8e4530" class="">Utilização</h3>
            <p id="0e4a97ff-41a1-42d3-a6db-573476406890" class="">Para utilização desse projeto é necessário seguir os seguintes passos:</p>
            <ul id="35f41597-8d43-4e36-86a4-b9a29f19c37a" class="bulleted-list">
                <li>Dentro da sua pasta htdocs clonar o repositório</li>
            </ul><pre id="abce35f5-a3d3-474a-9153-04ab80effd0b" class="code"><code>git clone https://github.com/tucoander/apontamentos.git</code></pre>
            <ul id="4e113568-b2d1-4513-b831-63a76ee9ef72" class="bulleted-list">
                <li>Iniciar o seu servidor Apache</li>
            </ul>
            <ul id="19cd0d19-d7e2-4f9d-aaa6-a0725019715e" class="bulleted-list">
                <li>Dentro do seu arquivo php.ini será necessário:
                    <ul id="0ace4832-544c-4ccf-bd5b-e9250b5321ee" class="bulleted-list">
                        <li>descomentar a linha extension=sqlite3</li>
                    </ul>
                    <ul id="0574dbf1-c7f5-4409-9089-14ea3cfe7ae4" class="bulleted-list">
                        <li>alterar a linha output_buffering = off para output_buffering = 4096</li>
                    </ul>
                </li>
            </ul>
        <p id="5a93792c-fd6f-46f3-ab3d-951bcf5e7dff" class=""></p>
   <h2 id="5afb65f0-908e-43a0-bbc0-8e74a2a64468" class="">Páginas da Aplicação</h2>
   <p id="e14f1800-3c3e-44b3-80af-8dc3a237e9ea" class="">Página principal para inserção/edição dos dados</p>
   <figure id="fd0ada68-b5f6-4805-b509-52c3c725b935" class="image"><a href="#"><img style="width:1921px" src="/img/readme/apontamentos-criar.png"></a></figure>
   <p id="ca3caa8e-4840-4d5a-9d6f-a6217dfcae36" class="">Dashboard para usuário</p>
   <figure id="4a184727-ee45-4874-af55-dd7158c7a45a" class="image"><a href="#"><img style="width:1898px" src="/img/readme/apontamentos-gráficos-usuário.png"></a></figure>
   <p id="288e51d4-092e-4044-bc8a-c7eed6fda68f" class="">Tela para consulta dos apontamentos</p>
   <figure id="a02d8c76-7beb-4457-8a22-0fec1fd2e309" class="image"><a href="#"><img style="width:1920px" src="/img/readme/apontamentos-edição.png.png"></a></figure>
   <p id="7ecee006-f86e-400f-88cc-0cd721cbf5af" class="">Dashboard para gestores</p>
   <figure id="f1c011d7-af35-47f5-bd0b-406daf80accc" class="image"><a href="#"><img style="width:1900px" src="/img/readme/apontamentos-dashboard-gestão.png"></a></figure>
   <p id="6dd8d4b8-ad44-4fe0-8af9-c0885c5e1b76" class="">Dashboard para gestores por localidade</p>
   <figure id="40ba2ecf-cc5e-49ea-b7b7-88a0c0b2bf33" class="image"><a href="#"><img style="width:1900px" src="/img/readme/apontamentos-dashboard-gestão-unidade.png.png"></a></figure>
   <figure class="block-color-gray_background callout" style="white-space:pre-wrap;display:flex" id="a558d42b-68ff-4d3c-abba-5dbf858803bf">
      <div style="font-size:1.5em"><span class="icon">💡</span></div>
      <div style="width:100%">Os usuários foram ocultados </div>
   </figure>
        </div>
