-- Procedure sprRelatorioAtendimentos corrigida com 7 parâmetros
-- Adiciona pAtendentes como 7º parâmetro

DROP PROCEDURE IF EXISTS sprRelatorioAtendimentos;

DELIMITER $$

CREATE PROCEDURE sprRelatorioAtendimentos(
  pDe DATE,
  pAte DATE,
  pSituacao VARCHAR(2),
  pEtiquetas VARCHAR(20),
  pNumero VARCHAR(100),
  pProtocolo VARCHAR(100),
  pAtendentes VARCHAR(20)
)
BEGIN
    IF pSituacao = 'A' THEN
        SELECT ta.id, ta.situacao, ta.dt_atend, ta.nome, ta.numero, u.nome as nome_atend, '' as etiqueta
        FROM tbatendimento ta
        INNER JOIN tbusuario u ON u.id = ta.id_atend
        WHERE dt_atend BETWEEN pDe AND pAte
        AND ta.situacao = 'A'
        AND ((COALESCE(pNumero,'') = '') OR (pNumero = ta.numero))
        AND ((COALESCE(pProtocolo,'') = '') OR (pProtocolo = ta.protocolo))
        AND ((COALESCE(pAtendentes,'0') = '0') OR (FIND_IN_SET(ta.id_atend, pAtendentes)))
        ORDER BY ta.dt_atend DESC, ta.nome;
    
    ELSEIF pSituacao = 'F' THEN
        SELECT ta.id, ta.dt_atend, ta.nome, ta.numero, ta.nome_atend,
        CONCAT(GROUP_CONCAT(CONCAT('<span style="border-radius:5px;background-color:',te.cor,'">',te.descricao, '</span>') SEPARATOR ',')) as etiqueta
        FROM tbatendimento ta
        LEFT JOIN tbatendimentoetiquetas tae ON tae.id_atendimento = ta.id AND tae.numero = ta.numero
        LEFT JOIN tbetiquetas te ON te.id = tae.id_etiqueta
        WHERE dt_atend BETWEEN pDe AND pAte
        AND (ta.situacao = 'F' AND COALESCE(ta.finalizado_por,'') <> 'Transferencia')
        AND ((COALESCE(pNumero,'') = '') OR (pNumero = ta.numero))
        AND ((COALESCE(pProtocolo,'') = '') OR (pProtocolo = ta.protocolo))
        AND ((pEtiquetas = '0') OR (FIND_IN_SET(te.id, pEtiquetas)))
        AND ((COALESCE(pAtendentes,'0') = '0') OR (FIND_IN_SET(ta.id_atend, pAtendentes)))
        GROUP BY ta.id, ta.dt_atend, ta.nome, ta.numero, ta.nome_atend
        ORDER BY ta.dt_atend DESC, ta.nome;
    
    ELSE
        -- Todas as situações (padrão)
        SELECT ta.id, ta.dt_atend, ta.nome, ta.numero, u.nome as nome_atend,
        CONCAT(GROUP_CONCAT(CONCAT('<span style="border-radius:5px;background-color:',te.cor,'">',te.descricao, '</span>') SEPARATOR ',')) as etiqueta
        FROM tbatendimento ta
        INNER JOIN tbusuario u ON u.id = ta.id_atend
        LEFT JOIN tbatendimentoetiquetas tae ON tae.id_atendimento = ta.id AND tae.numero = ta.numero
        LEFT JOIN tbetiquetas te ON te.id = tae.id_etiqueta
        WHERE dt_atend BETWEEN pDe AND pAte
        AND ((ta.situacao = 'A') OR (ta.situacao = 'F' AND COALESCE(ta.finalizado_por,'') <> 'Transferencia'))
        AND ((COALESCE(pNumero,'') = '') OR (pNumero = ta.numero))
        AND ((COALESCE(pProtocolo,'') = '') OR (pProtocolo = ta.protocolo))
        AND ((pEtiquetas = '0') OR (FIND_IN_SET(te.id, pEtiquetas)))
        AND ((COALESCE(pAtendentes,'0') = '0') OR (FIND_IN_SET(ta.id_atend, pAtendentes)))
        GROUP BY ta.id, ta.dt_atend, ta.nome, ta.numero, u.id, u.nome
        ORDER BY ta.dt_atend DESC, ta.nome;
    
    END IF;
END $$

DELIMITER ;
