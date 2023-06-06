DROP TRIGGER before_update_uitslag_trigger;
DELIMITER //
CREATE TRIGGER before_update_uitslag_trigger BEFORE UPDATE ON uitslag
FOR EACH ROW
BEGIN
  IF OLD.cijfer <> NEW.cijfer THEN
    INSERT INTO uitslag_log (studentid, naam, werkproces, cijfer, old_cijfer, resultaat)
      SELECT s.id, s.naam, u.werkproces, NEW.cijfer, OLD.cijfer, u.resultaat
      FROM uitslag u
      JOIN student s on s.id=u.studentid
      WHERE u.id=OLD.id;
    END IF;
END //
DELIMITER ;