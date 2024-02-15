import { Directive, ElementRef, Renderer2 } from '@angular/core';
@Directive({
  selector: '[appRoundBlock]',
})
export class RoundBlockDirective {
  constructor(renderer: Renderer2, elmRef: ElementRef) {
    renderer.setStyle(elmRef.nativeElement, 'border-radius', '100px');
  }
}
