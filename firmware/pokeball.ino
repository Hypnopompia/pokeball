Servo myservo;
void setup() {
    myservo.attach(D1);
    pinMode(D7, OUTPUT);
}


void loop()
{
    delay(500);

    digitalWrite(D7, HIGH);
    myservo.write(60);
    delay(300);

    digitalWrite(D7, LOW);
    myservo.write(120);
    delay(500);

    myservo.write(90);
}
