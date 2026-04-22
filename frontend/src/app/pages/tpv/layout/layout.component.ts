import { Component, OnInit } from '@angular/core';
import { Router, RouterOutlet } from '@angular/router';
import { CommonModule } from '@angular/common';
import { AuthService } from '../../../services/auth/auth.service';

@Component({
  selector: 'app-layout',
  standalone: true,
  templateUrl: './layout.component.html',
  styleUrls: ['./layout.component.scss'],
  imports: [CommonModule, RouterOutlet],
})
export class LayoutComponent implements OnInit {
  user: any = null;

  constructor(private authService: AuthService, private router: Router) {}

  ngOnInit() {
    this.user = this.authService.getUser();
    console.log('USER LAYOUT:', this.user);
  }

  logout() {
    this.authService.logout();
    this.router.navigate(['/login']);
  }

  goToTables() {
    this.router.navigate(['/tpv/tables']);
  }

  goToSettings() {
    this.router.navigate(['/tpv/settings']);
  }

  isAdmin(): boolean {
    return this.user?.role?.toLowerCase?.() === 'admin';
  }

  isRouteActive(route: string): boolean {
    return this.router.url.includes(route);
  }
}