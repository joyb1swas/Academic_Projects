#include <iostream>
using namespace std;
class math
{
public:
    void display();
};

void math ::display()
{
    int formula;
    cout << "Enter 1 to see Linears formulas or 2 to see Non Linear formulas " << endl;
    cout << "\n";

    cin >> formula;
    switch (formula)
    {

    case 1:
        cout << "\n";
        cout << "Linear formulas are" << endl;
        cout << "\n";
        cout << "1. y = mx + c ....................(for one variable) " << endl;
        cout << "\n";
        cout << "2. y = a1x1 + a2x2 + a3x3 + .... (for multiple variables) " << endl;
        break;

    case 2:

        cout << "\n";
        cout << "Non Linear formulas are" << endl;
        cout << "\n";
        cout << "1. y = ax^2 + bx + c     (Quadric formula)" << endl;
        cout << "\n";
        cout << "2. y = a * e^(bx)        (Exponential formula)" << endl;
        cout << "\n";
        cout << "3. y = a * sin(bx + c)   (Trigonometric formula)" << endl;
        break;

    default:
        cout << "Invalid formula " << endl;
    }
}

int main()
{
    math obj;
    obj.display();

    return 0;
}