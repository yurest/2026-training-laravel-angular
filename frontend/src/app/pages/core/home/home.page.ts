import { Component, OnInit } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { IonHeader, IonToolbar, IonTitle, IonContent } from '@ionic/angular/standalone';

@Component({
  selector: 'app-home',
  templateUrl: 'home.page.html',
  styleUrls: ['home.page.scss'],
  imports: [IonHeader, IonToolbar, IonTitle, IonContent],
})
export class HomePage implements OnInit {
  constructor(private http: HttpClient) {}

  ngOnInit() {
    this.testRequest();
  }

  testRequest() {
    this.http.get('http://localhost:8000/api/users').subscribe({
      next: (response) => {
        console.log('OK', response);
      },
      error: (error) => {
        console.log('ERROR', error);
      }
    });
  }
}