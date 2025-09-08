<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
        <!-- Styles -->
    </head>



  <body>
    <!-- component -->

    <!-- This is an example component -->
    <div class="bg-grayDark" x-data="{ tab: 'index' }">
      <!-- Navbar -->
      <nav class="w-full text-white bg-gray-700 fixed top-0 animated z-40 shadow-lg">
        <div class="-mb-px flex">
          <div class="py-1 px-3 text-yellow-400 text-sm font-semibold">{{isset($user->company) && strlen($user->company)>1 ? $user->company : config('app.name')}}</div>
        </div>
        <div class="-mb-px flex">

          <a onclick="showView('view-init')" class="w-1/5 cursor-pointer text-center no-underline border-b-2 border-yellow-400 py-1 mx-1">
          <img  class="block m-auto h-9" src="{{ asset('uploads/logo250.png') }}" alt="logo">
          </a>


          <a onclick="showView('view-prod')" style="font-size:10px" class="w-1/5 cursor-pointer text-center no-underline border-b-2 border-transparent uppercase py-1 mx-1">
            <svg class="block m-auto" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" height="20" width="20">
              <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd" />
            </svg> PRODUCTOS
          </a>

          <a onclick="loadCart(1)" style="font-size:10px" class="w-1/5 cursor-pointer text-center no-underline border-b-2 border-transparent uppercase py-1 mx-1">
           <svg class="block m-auto" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" height="20" width="20">
            <path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3zM16 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM6.5 18a1.5 1.5 0 100-3 1.5 1.5 0 000 3z" />
          </svg> CARRITO
          </a>

          <a onclick="showView('view-user');" style="font-size:10px" class="w-1/5 cursor-pointer text-center no-underline border-b-2 border-transparent uppercase py-1 mx-1">
          <svg class="block m-auto" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" height="20" width="20">
            <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
            <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd" />
          </svg>PEDIDOS
          </a>

          <a onclick="showView('view-cont')" style="font-size:10px" class="w-1/5 cursor-pointer text-center no-underline border-b-2 border-transparent uppercase py-1 mx-1">
          <svg class="block m-auto" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" height="20" width="20">
            <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
          </svg> CONTACTO
          </a>

          <a onclick="expandSearch()" style="font-size:10px" class="w-1/5 cursor-pointer text-center no-underline border-b-2 border-transparent uppercase py-1 mx-1">
          <svg class="block m-auto" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="grey" height="30" width="30">
            <path d="M9 9a2 2 0 114 0 2 2 0 01-4 0z" />
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a4 4 0 00-3.446 6.032l-2.261 2.26a1 1 0 101.414 1.415l2.261-2.261A4 4 0 1011 5z" clip-rule="evenodd" />
          </svg>
          </a>

        </div>
        <div id="boxSearch" class="w-full fixed top-18 bg-gray-700 bg-gray-300 hidden">
          <div class="relative text-gray-600 py-2 px-4">
            <input type="search" id="searchpro" placeholder="Search" class="bg-white w-11/12 h-8 p-2 pr-10 rounded-full text-sm focus:outline-none">
            <button onclick="expandSearch()" type="submit" class="absolute right-0 top-2 mt-2 mr-4">
              <svg class="h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
        </div>
      </nav>
      <!-- End Navbar -->
      <div class="w-full md:pl-4 lg:pl-10 md:pr-4 flex self-start flex-col mt-20">
        <!-- view-init -->
        <div class="aviews flex flex-col w-full -my-2" id="view-init">
              <div class="w-full bg-cover bg-center" style="height:200px; background-image: url({{ asset('img/left-fd-background.jpeg') }});">
                <div class="flex items-center justify-center h-full w-full">

                </div>
            </div>
              <div class="grid grid-cols-2 gap-4 p-2">

                          <div  class="flex flex-col items-start"  onclick="loadProducts(1)">
                              <div class="bg-white rounded-lg">

                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="Gray">
                                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                              </div>
                              <div class="bg-white shadow-lg rounded -mt-4 mx-2">
                                <div class="px-2">
                                  <span class="font-bold text-gray-800">title</span>
                                </div>
                              </div>
                          </div>
              </div>
        </div>
        <!-- view-prod -->
        <div class="aviews flex flex-col items-center px-2 w-full hidden" id="view-prod">


        </div>
        <!-- view-item -->
        <div class="aviews flex flex-col items-center px-2 w-full hidden" id="view-item">
          <div class="fixed top-180 left-5 px-2"> <button onclick="showView('view-prod')" type="button" class="bg-white rounded px-4 py-2"> < Volver </button> </div>
          <div  class="flex flex-col items-center py-8">
            <div class="bg-white rounded-lg">
              <img src="" class="w-full rounded-md" id="itemImg"/>
            </div>
            <div class="bg-white shadow-lg rounded-lg -mt-4 w-11/12">
              <div class="py-4 px-4">
                <span class="font-bold text-gray-800 text-lg" id="itemTitle">Titulo</span>
                  <div class="text-sm text-gray-600 font-light" id="itemDescrip">Descripcion</div>
                  <div id="boxHTML" class="py-2"></div>
                  <div class="py-2">
                    <span class="text-blue-600">Nota:</span>
                    <textarea class="rounded border-gray-400 w-full" name="noteAdd" id="noteAdd" rows="1"></textarea>
                    <br>

                  </div>
              </div>
            </div>
          </div>

          <div class="fixed bottom-0 w-full px-2">
            <button onclick="addCart()" type="button" class="border w-full font-bold border-green-600 bg-green-600 text-white rounded-md py-2 my-2 hover:bg-green-700 focus:outline-none focus:shadow-outline">
              Agregar Producto</button>
          </div>



        </div>
        <!-- view-cart -->
        <div class="aviews flex flex-col items-center px-2 w-full hidden" id="view-cart">Carrito</div>

        <!-- view-user -->
        <div class="aviews flex flex-col items-center px-2 w-full hidden" id="view-user">

          <div id="view-login" class="oviews container mx-auto flex flex-col items-center">
              <form id="form_init" class="shadow-md w-80 p-4 flex flex-col bg-white rounded-md mt-6">
                  <div class="text-center mb-2"> <h6 class="text-gray-600 text-sm font-bold">INICIAR SESIÓN</h6> </div>
                  <input type="email" name="email" placeholder="Email" class="mb-2 py-2 px-4 border border-gray-400 focus:outline-none rounded-md focus:ring-1 ring-cyan-500" />
                  <p class="hidden text-xs italic text-red-500 mb-2">Please choose a password.</p>
                  <input type="text" name="password" placeholder="Password" class="mb-2 py-2 px-4 border border-gray-400 focus:outline-none rounded-md focus:ring-1 ring-cyan-500" />
                  <p class="hidden text-xs italic text-red-500 mb-2">Please choose a password.</p>
                  <a onclick="userInit()" class="w-full mb-2 bg-blue-500 text-white p-2 rounded font-semibold text-md">Ingresar</a>
                  <hr class="mb-2" />
                  <a onclick="showForm('view-regis')" class="text-blue-700 p-2 font-semibold">Registrarme</a>
              </form>
          </div>

          <div id="view-regis" class="oviews hidden container mx-auto flex flex-col items-center">
              <form id="form_regis" class="shadow-md w-80 p-4 flex flex-col bg-white rounded-md mt-6">
                  <div class="text-center mb-2"> <h6 class="text-gray-600 text-sm font-bold">REGISTRO DE USUARIO</h6> </div>
                  <input type="text" name="name" placeholder="Nombre" class="mb-2 py-2 px-4 border border-gray-400 focus:outline-none rounded-md focus:ring-1 ring-cyan-500" />
                  <p class="hidden text-xs italic text-red-500 mb-2">Please choose a password.</p>
                  <input type="email" name="email" placeholder="Email" class="mb-2 py-2 px-4 border border-gray-400 focus:outline-none rounded-md focus:ring-1 ring-cyan-500" />
                  <p class="hidden text-xs italic text-red-500 mb-2">Please choose a password.</p>
                  <input type="password" name="password" placeholder="Password" class="mb-2 py-2 px-4 border border-gray-400 focus:outline-none rounded-md focus:ring-1 ring-cyan-500" />
                  <p class="hidden text-xs italic text-red-500 mb-2">Please choose a password.</p>
                  <input type="password" name="password_confirmation" placeholder="Password Confirmation" class="mb-2 py-2 px-4 border border-gray-400 focus:outline-none rounded-md focus:ring-1 ring-cyan-500" />
                  <p class="hidden text-xs italic text-red-500 mb-2">Please choose a password.</p>
                  <a onclick="userRegis()" class="w-full mb-2 bg-blue-500 text-white p-2 rounded font-semibold text-md">Registrarme</a>
                  <hr class="mb-2" />
                  <a onclick="showForm('view-login')" class="text-blue-700 p-2 font-semibold">Ya tengo una cuenta. Iniciar Sesión.</a>
              </form>
          </div>

          <div id="view-order" class="oviews hidden container mx-auto flex flex-col items-center">

            <div class="text-center mb-2"> <h6 class="text-gray-600 text-sm font-bold">MIS PEDIDOS</h6> </div>

                <div class="p-2 my-1 border border-gray-300 rounded-md shadow-md w-full">
                  <div class="text-xs py-1 px-2 text-gray-600" >20 Feb 2021 <div class="float-right text-gray-600 order-number">Pedido #123456</div></div>
                  <div class="text-xs py-1 px-2 text-gray-800 font-bold" >Total: 0$    <div class="float-right text-green-700 order-total">Procesado</div></div>
                </div>
                <div><a href="javascript:userDele()" class="text-red-600">Cerrar Sesión</a> </div>

          </div>


        </div>
        <!-- view-cont -->
        <div class="aviews flex flex-col items-center px-2 w-full hidden" id="view-cont">
          @isset($user->htmlabout) <?php echo htmlspecialchars_decode($user->htmlabout)?> @endisset
          <a href="javascript:test2()">*</a>
        </div>
      </div>
    </div>


    <!-- Temp List Products  -->
    <template id="tempListPro">
      <div class="w-full shadow-lg overflow-hidden rounded my-1 border principal">
        <div class="flex">
          <div class="flex-none w-1/6 relative">
            <img src="preview.svg" class="imgdir absolute inset-0 w-full h-full object-cover rounded"/>
          </div>
          <div class="flex-auto p-2 cuerpo">
              <div class="flex flex-wrap">
                <h1 class="text-blue font-bold flex-auto text-sm titulo">Titulo Producto</h1>
                <div class="w-full text-xs text-gray-500 mt-1 descripcion">descripcion Producto</div>
              </div>
          </div>
        </div>
      </div>
    </template>

    <template id="tempListProPrice">
            <span class="text-xs m-1">
                <span class="text-gray-500 detalle">Detalle</span><span class="text-gray-600">  </span><span class="text-red-800 font-bold precio">Precio</span>
            </span>
    </template>

    <!-- Temp Item Prices Radio Buttoms-->
    <template id="tempItemProPriceRadioButtoms">
            <div class="flex optProds justify-center border border-yellow-500 rounded hover:bg-yellow-500 focus:outline-none focus:shadow-outline">
                <input name="quantity" type="radio" class="hidden vradio">
                <label class="px-2 py-1">
                    <span class="detalle">Normal</span> @if(isset($user->coin)){{$user->coin.'.'}}@else{{'$.'}}@endif<span class="precio font-bold">100.00</span>
                </label>
            </div>
    </template>

    <template id="tempItemProPriceSimple">
      <div class="flex">
        <div class="flex-grow ">
            <span class="text-xs m-1">
                <span class="text-gray-500 detalle">Detalle</span>
                <span class="text-gray-600">@if(isset($user->coin)){{$user->coin.'.'}}@else{{'$.'}}@endif</span>
                <span class="text-red-800 font-bold precio">Precio</span>
            </span>
        </div>
        <div class="flex-none w-28">
            <div class="flex flex-row border h-10 w-28 rounded-lg border-gray-400 relative">
                <button class="btnsub font-semibold text-lg border-r bg-red-700 hover:bg-red-600 text-white border-gray-400 h-full w-16 flex rounded-l focus:outline-none">
                    <span class="m-auto">-</span>
                </button>
                <input class="inpcant bg-white w-10 flex text-center appearance-none font-semibold " name="quantity[]" value="0" readonly/>
                <button class="btnadd font-semibold text-center text-lg border-l bg-red-700 hover:bg-red-600 text-white border-gray-400 h-full w-16 flex rounded-r focus:outline-none">
                    <span class="m-auto">+</span>
                </button>
            </div>
        </div>
    </div>
    </template>

    <template id="tempItemCartPro">
        <div class="p-2 my-1 border border-gray-300 rounded-md shadow-md w-full">
          <div class="divide-y divide-gray-200 divide-solid">
              <div>
                  <div class="float-right w-5 h-5 mb-6 hover:bg-red-200 rounded-full cursor-pointer text-red-700 cart-del">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                  </div>
                  <div class="text-xs font-bold cart-title">Titulo</div>
                  <div class="text-xs py-1 text-gray-600 cart-descrip">Descripcion</div>
                  <div class="conto-note"></div>
              </div>
              <div class="text-xs py-1 text-gray-400 cart-price"></div>
              <div class="add-adic"></div>
          </div>
        </div>
    </template>

  <template id="tempCartTotal">
    <div class="p-2 my-1 border border-gray-300 rounded-md  w-full shadow-md">
      <div class="divide-y divide-gray-200 divide-solid">
        <div class="text-xs py-1 text-gray-800" >Subtotal: <div class="float-right text-gray-800 cart-subtotal">$99.999.999</div></div>
        <div class="text-xs py-1 text-gray-800 text-yellow-600 font-bold cart-descuento"></div>
        <div class="text-xs py-1 text-gray-800" >Total:    <div class="float-right text-gray-800 font-bold cart-total">$99.999.999</div></div>
        </div>
    </div>
    <button type="button" onclick="sendOrderWsapp()" class="border border-green-600 bg-green-600 text-white rounded-md px-4 py-2 m-2 w-full">ENVIAR PEDIDO</button>
  </template>

  <template id="tempSelCont">
      <div class="flex flex-row h-8 w-full my-2">
          <select class="selcont rounded leading-none text-sm w-full flex"></select>
      </div>
  </template>


  <template id="tempSelAdic">
    <div class="flex flex-row h-8 w-full my-2">
        <select class="selcont rounded-l leading-none text-sm w-full flex"></select>
        <button onclick="delrowaddon(this)" class="btnadd font-semibold text-center bg-red-700 hover:bg-red-600 text-white h-full w-16 flex rounded-r focus:outline-none">
            <span class="m-auto">-</span>
        </button>
    </div>
</template>


  <template id="tempBtnAdic">
    <div>
      <div class="text-red-800">Adicionales</div>
      <div id="sectionAdic"></div>
        <button class="btn-adicional w-9/12 border border-red-600 bg-white text-red-600 rounded-md py-1 my-2">Agregar Adicional</button>
    </div>
</template>


    <script src="{{ asset('js/store.js?v='.uniqid()) }}" defer></script>
    <script>

        const allCat = [];
        const allpro =  [];

        const urInit = "login";
        const urUser = "user";
        const urRegi = "register";
        const urOrder= "sendorder";

        const device = @if(isset($device)) "{{$device}}" @else 'Desktop' @endif;
        const coin = @if(isset($user->coin)) "{{$user->coin}}" @else "$" @endif;
        const porcDesc = @if( isset($user->discount) && filter_var($user->discount,FILTER_SANITIZE_NUMBER_INT)>0) {{filter_var($user->discount,FILTER_SANITIZE_NUMBER_INT)}} @else 0 @endif;
        const nroWhatsapp = @if( isset($user->whatsapp) && strlen($user->whatsapp)>6) "{{$user->whatsapp}}" @else "+593978927096" @endif;

        document.addEventListener("DOMContentLoaded", function(event) {
                //getOrders();
        });
        const test2 = () =>{ console.log(localStorage.getItem('AppStore'));  }
        window.onbeforeunload = function(e) {
            return "Seguro que deseas salir?"
        }
    </script>


<template id="XXXXXXXXX">
  <div id="delete" class="w-9/12"></div>
  <div class="p-2 my-1 border border-gray-300 rounded-md shadow-md">
    <div class="divide-y divide-gray-200 divide-solid">
        <div>
            <div class="float-right w-5 h-5 mb-6 hover:bg-red-200 rounded-full cursor-pointer text-red-700">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
              </svg>
            </div>
            <div class="text-xs font-bold cart-title text-3xl text-2xl text-1xl">BASICA DE POLLO. SIN PAPAS. SIN VEGETALES</div>
            <div class="text-xs py-1 text-gray-600 cart-descrip">1 Pechuga de Pollo + pan de la casa + tres salsas (tomate, mayonesa y mostaza)</div>
        </div>
        <div class="text-xs py-1 text-blue-600">Nota: Hola mundo</div>
        <div class="text-xs py-1 text-gray-400" >Normal: $99.999.999 x Cant:2  <div class="float-right text-gray-800 font-bold">$99.999.999</div></div>
    </div>
    <div class="grid-cols-3 bg-yellow-300"></div>
  </div>
</template>
  </body>


</html>
