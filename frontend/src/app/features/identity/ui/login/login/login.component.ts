import { Component, OnInit } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { Router } from '@angular/router';
import { AuthService } from '../../../infrastructure/auth.service';

@Component({
  selector: 'app-login',
  standalone: true,
  imports: [FormsModule],
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.scss'],
})
export class LoginComponent implements OnInit {
  email = '';
  password = '';

  constructor(private authService: AuthService, private router: Router) {}

  ngOnInit() {
    //  si ya estás logueado, no puedes volver al login
    if (this.authService.isLoggedIn()) {
      this.router.navigate(['/tpv']);
    }
  }

  login() {
    this.authService.login(this.email, this.password).subscribe({
      next: (response: any) => {
        this.authService.saveToken(response.token);
        this.authService.saveUser(response.user);

        console.log(response);

        // redirige al TPV
        this.router.navigate(['/tpv']);
      },
      error: (error) => {
        console.log(error);
      },
    });
  }
}