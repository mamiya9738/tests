#include <stdio.h>

static void init_f();
static unsigned long long map[10000];
static unsigned long long  f(unsigned  long long n);

int main(int argc, char const *argv[])
{
  init_f();
  int n = 8181LL;
  printf("answer = %llu ",f(n));
  return 0;
}

void init_f()
{
  for(int i=0;i<10000;i++)
  {
    map[i]=-1;  //初期化
  }
}
unsigned long long  f(unsigned long long  n)
{
  switch (n)
  {
    case 0 :
      return 0;
    case 1 :
      return 1;
    default :
      if(map[n]!=-1)
      {
        return map[n];
      }
      map[n] = f(n-1) + f(n-2);
      return map[n];
  }
}
