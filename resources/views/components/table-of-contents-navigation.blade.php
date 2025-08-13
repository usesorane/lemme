<nav {{ $attributes->merge(['class' => '']) }}
  x-data="{
    init() {
      $nextTick(() => {
        const controls = Array.from(this.$el.querySelectorAll('[data-slot=control]'));
        if (!controls.length) return;

        const first = controls[0];
        const hash = window.location.hash;

        if (hash) {
          const targetLink = this.$el.querySelector(`[data-slot=link][href='${hash}']`);
          if (targetLink) {
            const targetControl = targetLink.closest('[data-slot=control]');
            if (targetControl) {
              controls.forEach(c => { if (c !== targetControl) c.dispatchEvent(new CustomEvent('link:inactive', { bubbles: false })); });
              targetControl.dispatchEvent(new CustomEvent('link:active', { bubbles: false }));
              return;
            }
          }
        }

        if (first) {
          first.dispatchEvent(new CustomEvent('link:active', { bubbles: false }));
        }
      });
    }
  }"
  x-init="init()"
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