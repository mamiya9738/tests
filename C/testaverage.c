#include <stdio.h>

#define AVERAGE_MAX 3
#define ARRAY_SIZE 5
#define AVERAGE_SIZE (ARRAY_SIZE - AVERAGE_MAX + 1)

static double getMovingAverage(const double *array , const unsigned short size );
static void debugPrint(const double *array , const unsigned short size);

int main(int argc, char const *argv[])
{
  const double array[ARRAY_SIZE] = {7.0,9.0,5.0,1.0,3.0};
  double average[AVERAGE_SIZE]={0.,0.,0.};

  debugPrint(array,ARRAY_SIZE);

  for(int i = 0 ; i < AVERAGE_SIZE ; i++)
  {
    average[i] = getMovingAverage(array + i , AVERAGE_MAX);
  }

  debugPrint(average,AVERAGE_SIZE);

  return 0;
}

void debugPrint(const double *array , const unsigned short size)
{
  printf("[");
  for(int i = 0 ; i < size ; i++)
  {
    printf("%lf",array[i]);
    if(i + 1 < size)
    {
      printf(",");
    }
  }
  printf("]\n");

}

double getMovingAverage(const double *array , const unsigned short size )
{
  double sum = 0;

  for(unsigned short  i = 0 ; i < size ;i++)
  {
    sum += array[i];
  }

  return sum / size;
}
