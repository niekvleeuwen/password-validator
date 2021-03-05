<div class="p-6 sm:px-20 bg-white border-b border-gray-200">
    <div>
        <x-jet-application-logo class="block h-12 w-auto" />
    </div>

    <div class="mt-8 text-2xl">
        Welcome to Password validator!
    </div>

    <div class="mt-6 text-gray-500">
        Password validator is a web application capable of validating passwords using Password validator is able to validate passwords against different sources. Several techniques are also used to determine the strength of the password. Some of the functionalities offered are:
    </div>

    <div class="mt-6 text-gray-500">
        <ul class="list-disc">
            <li>Check if the password has been exposed in data breaches.</li>
            <li>Calculate the time it takes to brute-force the password.</li>
        </ul>
    </div>

    @guest
        <x-jet-nav-link class="mt-6" href="{{ route('login') }}" :active="request()->routeIs('login')">
            {{ __('Create an account') }}
        </x-jet-nav-link>
    @endguest
</div>
