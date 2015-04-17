USE StudyTree;
DROP VIEW IF EXISTS v_chat_groups;

CREATE VIEW v_chat_groups AS

SELECT short_desc as name, uuid 
	FROM study_groups 
		UNION 
SELECT name, uuid FROM v_class;
