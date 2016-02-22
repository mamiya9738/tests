#include "stdlib.h"
#include "stdio.h"


int main(int argc, char const *argv[])
{
  /* code */
  long ll=0x12345678L;

  ll <<= (4&6);
  printf("%lx,%d",ll,4&6);
  return 0;
}
