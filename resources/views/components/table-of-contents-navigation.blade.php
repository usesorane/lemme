 <nav {{ $attributes->merge(['class' => '']) }} 
      x-data=""
      x-on:click="let target = $event.target; let control = target.closest('[data-slot=control]'); let link = target.closest('[data-slot=link]'); console.log(target); console.log(control); console.log('===');"
      >
    {{ $slot }}
 </nav>