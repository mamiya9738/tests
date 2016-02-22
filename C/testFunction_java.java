import java.util.Map;
import java.util.HashMap;


public class testFunction_java
{
  private static Map<Integer,Integer> map ;
  public static  void main(String[] args)
  {
    map = new HashMap();
    System.out.println(f(8181));

  }

  public static Integer f(Integer n)
  {
    switch (n)
    {
      case 0 :
        return 0;
      case 1 :
        return 1;
      default :
        if(map.containsKey(n))
        {
          for(Map.Entry<Integer, Integer> m : map.entrySet())
          {
            if(m.getKey() == n)
            {
              return m.getValue();
            }
          }
        }
        Integer v = f(n-1) + f(n-2);
        System.out.print(n);
        System.out.print(",");
        System.out.println(v);

        map.put(n,v);
        return v;
    }
  }
}
