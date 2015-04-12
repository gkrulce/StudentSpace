USE StudyTree;
DROP VIEW IF EXISTS v_class;

CREATE VIEW v_class AS
SELECT c.id, CONCAT(d.short_name, " ", c.number) name, c.uuid FROM classes c 
	JOIN departments d on d.id = c.department_id
