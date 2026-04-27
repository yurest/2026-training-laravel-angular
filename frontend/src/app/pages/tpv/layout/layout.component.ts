import { Component, OnInit } from '@angular/core';
import { Router, RouterOutlet } from '@angular/router';
import { CommonModule } from '@angular/common';
import { IonButton, IonIcon } from '@ionic/angular/standalone';
import { addIcons } from 'ionicons';
import { moon, sunny } from 'ionicons/icons';
import { AuthService } from '../../../services/auth/auth.service';

@Component({
  selector: 'app-layout',
  standalone: true,
  templateUrl: './layout.component.html',
  styleUrls: ['./layout.component.scss'],
  imports: [CommonModule, RouterOutlet, IonButton, IonIcon],
})
export class LayoutComponent implements OnInit {
  user: any = null;
  isDarkTheme = true;

  constructor(private authService: AuthService, private router: Router) {
    addIcons({ moon, sunny });
  }

  ngOnInit(): void {
    this.user = this.authService.getUser();
    this.isDarkTheme = document.body.classList.contains('dark');
  }

  logout(): void {
    this.authService.logout();
    this.router.navigate(['/login']);
  }

  goToTables(): void {
    this.router.navigate(['/tpv/tables']);
  }

  goToSettings(): void {
    this.router.navigate(['/tpv/settings']);
  }

  isAdmin(): boolean {
    return this.user?.role?.toLowerCase?.() === 'admin';
  }

  isRouteActive(route: string): boolean {
    return this.router.url.includes(route);
  }

  toggleTheme(): void {
    this.isDarkTheme = !this.isDarkTheme;

    if (this.isDarkTheme) {
      document.body.classList.add('dark');
      document.body.classList.remove('light');
      localStorage.setItem('theme', 'dark');
    } else {
      document.body.classList.add('light');
      document.body.classList.remove('dark');
      localStorage.setItem('theme', 'light');
    }
  }
}