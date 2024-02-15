import { Injectable } from '@angular/core';
import { Overlay } from '@angular/cdk/overlay';
import { OverlayRef } from '@angular/cdk/overlay';
import { ComponentPortal } from '@angular/cdk/portal';
import { LoaderComponent } from 'src/app/shared/admin/loader/loader.component';
@Injectable({
  providedIn: 'root',
})
export class LoaderService {
  private overlayRef!: OverlayRef;
  constructor(private overlay: Overlay) {}

  showLoader() {
    this.overlayRef = this.overlay.create({
      positionStrategy: this.overlay
        .position()
        .global()
        .centerHorizontally()
        .centerVertically(),
      hasBackdrop: true,
    });
    this.overlayRef.attach(new ComponentPortal(LoaderComponent));
  }
  hideLoader() {
    this.overlayRef.detach();
  }
}
