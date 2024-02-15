import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { LayoutRoutingModule } from './layout-routing.module';
import { LayoutComponent } from './layout.component';
import { SharedModule } from '../shared/admin/shared.module';
import { SnackbarService } from './services/snackbar.service';
import { LoaderService } from './services/loader.service';
import { LoaderComponent } from '../shared/admin/loader/loader.component';
import { ConfirmationDialogComponent } from '../shared/admin/components/confirmation-dialog/confirmation-dialog.component';

@NgModule({
  declarations: [LayoutComponent, ConfirmationDialogComponent],
  imports: [CommonModule, LayoutRoutingModule, SharedModule],
  providers: [SnackbarService, LoaderService],
  entryComponents: [LoaderComponent, ConfirmationDialogComponent],
})
export class LayoutModule {}
