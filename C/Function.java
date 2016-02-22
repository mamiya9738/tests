public class Function
{
  public void f(int n)
  {
    switch (n)
    {
      case 0 :
        return 0;
      case 1 :
        return 1;
      default :
        return  this.f(n-1)+this.f(n-2);
    }

  }
}
