#include <stdio.h>

int main(int argc, char const *argv[]) {
  /* code */
  long a = 30;
  long b = 20;
  long *buff;

  buff = &b;
  *buff = 10;
  printf("b = [%ld]",b);

  buff = &a;
  *buff = b;
  printf("a = [%ld]",a);


  return 0;
}
