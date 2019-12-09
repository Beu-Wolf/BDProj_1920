select U.tipo, L.lingua, T.dia_da_semana, count(tipo_anomalia) 
from f_anomalia 
natural join d_utilizador as U 
natural join d_tempo as T 
natural join d_lingua as L 
group by cube(U.tipo, L.lingua, T.dia_da_semana);