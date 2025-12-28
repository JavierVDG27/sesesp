<nav x-data="{ open: false }" class="bg-white border-b border-[#9F2241] font-[Montserrat]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="flex justify-between h-16">

            <!-- Left side -->
            <div class="flex items-center space-x-4">

                <!-- Logo -->
                <a href="{{ dashboard_route() }}" class="flex items-center space-x-2">
                    <img src="{{ asset('images/logo.jpg') }}" alt="Logo" class="h-14 w-auto">
                </a>

                <!-- Dashboard Link -->
                <div class="hidden sm:flex sm:space-x-8 sm:ms-10">
                    <a 
                        href="{{ dashboard_route() }}"
                        class="inline-flex items-center px-1 pt-1 text-lg font-semibold tracking-wide text-black
                               hover:text-[#9F2241] border-b-2 border-transparent hover:border-[#9F2241]
                               transition"
                    >
                        Dashboard
                    </a>
                </div>

            </div>

            <!-- Right side (Settings dropdown) -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <div class="relative" x-data="{ open: false }" @click.outside="open = false">

                    <button @click="open = !open"
                            class="flex items-center text-lg font-semibold text-black hover:text-[#9F2241] transition">

                        <div>
                            {{ Auth::user()->nombres }}
                            - {{ ucfirst(Auth::user()->role->name) }}
                            @if(Auth::user()->institucion)
                                | {{ Auth::user()->institucion->siglas }}
                            @endif
                        </div>

                        <div class="ms-1">
                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 
                                        111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                    clip-rule="evenodd"/>
                            </svg>
                        </div>

                    </button>

                    <!-- Dropdown -->
                    <div x-show="open"
                        x-transition
                        class="absolute z-50 mt-2 w-48 rounded-md shadow-lg end-0 bg-white">

                        <div class="rounded-md shadow-lg ring-1 ring-black ring-opacity-5 py-1 bg-[#9F2241]">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button
                                    class="w-full text-left px-4 py-2 text-sm text-white hover:bg-[#691C32]">
                                    Cerrar sesión
                                </button>
                            </form>

                        </div>
                    </div>

                </div>
            </div>

        </div>

    </div>
</nav>
