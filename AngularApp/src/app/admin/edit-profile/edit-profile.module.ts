import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { EditProfileRoutingModule } from './edit-profile-routing.module';
import { SharedModule } from 'src/app/shared/admin/shared.module';
import { EditProfileComponent } from './edit-profile.component';

@NgModule({
  declarations: [EditProfileComponent],
  imports: [CommonModule, EditProfileRoutingModule, SharedModule],
})
export class EditProfileModule {}
