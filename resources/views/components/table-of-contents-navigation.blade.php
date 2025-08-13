<nav {{ $attributes->merge(['class' => '']) }}
    x-data=""
    x-on:click="
      const control = $event.target.closest('[data-slot=control]');
      if (!control || !$el.contains(control)) return;
      const allControls = $el.querySelectorAll('[data-slot=control]');
      const otherControls = Array.from(allControls).filter(c => c !== control);
      otherControls.forEach(c => c.dispatchEvent(new CustomEvent('link:inactive', { bubbles: false })));
    "
>
    {{ $slot }}
 </nav>