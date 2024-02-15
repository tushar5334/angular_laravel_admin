import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { MaterialModule } from './material.module';
import { HttpClientModule } from '@angular/common/http';
import { HeaderComponent } from './components/header/header.component';
import { FooterComponent } from './components/footer/footer.component';
import { SidebarComponent } from './components/sidebar/sidebar.component';
import { FlexLayoutModule } from '@angular/flex-layout';
import { RouterModule } from '@angular/router';
import { RoundBlockDirective } from './directives/round-block.directive';
//import { StylePaginatorDirective } from './directives/style-paginator.directive';
import { StylePaginatorDirective } from './directives/app-style-paginator.directive';
import { FileDragNDropDirective } from './directives/file-drag-n-drop.directive';
import { LayoutModule } from '@angular/cdk/layout';
import { OverlayModule } from '@angular/cdk/overlay';
import { LoaderComponent } from './loader/loader.component';

const sharedComponents = [
  HeaderComponent,
  FooterComponent,
  SidebarComponent,
  LoaderComponent,
];

const sharedDirectives = [
  RoundBlockDirective,
  StylePaginatorDirective,
  FileDragNDropDirective,
];

const sharedImports = [
  CommonModule,
  MaterialModule,
  HttpClientModule,
  FlexLayoutModule,
  LayoutModule,
  RouterModule,
  FormsModule,
  ReactiveFormsModule,
  OverlayModule,
];

@NgModule({
  declarations: [...sharedComponents, ...sharedDirectives],
  imports: [...sharedImports],
  exports: [...sharedComponents, ...sharedDirectives, ...sharedImports],
})
export class SharedModule {}
