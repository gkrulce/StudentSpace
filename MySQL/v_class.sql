USE StudyTree;
DROP VIEW IF EXISTS v_class;

CREATE VIEW v_class AS
SELECT c.id, CONCAT(d.short_name, " ", c.number) name FROM users u
	join users_to_classes uc on u.id=uc.user_id
	join classes c on c.id = uc.class_id
	join departments d on d.id = c.department_id
