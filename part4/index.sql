-- 1
CREATE INDEX idx_data_hora ON proposta_de_correcao USING btree (data_hora);

-- 3
CREATE INDEX idx_anomalia_id ON correcao USING btree (anomalia_id);

-- 4
CREATE INDEX idx_ts_language ON anomalia
       USING btree (tem_anomalia_redacao, ts, language);
