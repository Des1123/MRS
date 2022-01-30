/*
	Run this T-SQL to create the scheduled job that enables the Movie Reservation System to update the status of the seats from "reserved" to "used" if the movie
	schedule is already elapsed.
*/

DECLARE @job_name NVARCHAR(128), @description NVARCHAR(512), @owner_login_name NVARCHAR(128), @database_name NVARCHAR(128);

SET @job_name = N'MRS - Update Seat Status';
SET @description = N'Use to update the status of the reserved seats into used when the schedule has already elapsed';
SET @owner_login_name = N'SA';
SET @database_name = N'MOVIE_RESERVATION';

-- Delete job if it already exists:
IF EXISTS(SELECT job_id FROM msdb.dbo.sysjobs WHERE (name = @job_name))
BEGIN
    EXEC msdb.dbo.sp_delete_job
        @job_name = @job_name;
END

-- Create the job:
EXEC  msdb.dbo.sp_add_job
    @job_name=@job_name, 
    @enabled=1, 
    @notify_level_eventlog=0, 
    @notify_level_email=2, 
    @notify_level_netsend=2, 
    @notify_level_page=2, 
    @delete_level=0, 
    @description=@description, 
    @category_name=N'[Uncategorized (Local)]', 
    @owner_login_name=@owner_login_name;

-- Add server:
EXEC msdb.dbo.sp_add_jobserver @job_name=@job_name;

-- Add step to execute SQL:
EXEC msdb.dbo.sp_add_jobstep
    @job_name=@job_name,
    @step_name=N'Execute SQL', 
    @step_id=1, 
    @cmdexec_success_code=0, 
    @on_success_action=1, 
    @on_fail_action=2, 
    @retry_attempts=0, 
    @retry_interval=0, 
    @os_run_priority=0, 
    @subsystem=N'TSQL', 
    @command=N'INSERT INTO MR_TRANSACTION_HISTORY 
				(
					MRD_ID,
					MRTS_ID,
					CUSTOMER_NAME,
					SEAT_LIST,
					STATUS,
					USER_FROM
				) 
				SELECT 
					MRD_ID AS MRD_ID,
					MRTS_ID AS MRTS_ID,
					CUSTOMER_NAME AS CUSTOMER_NAME,
					SEAT_LIST AS SEAT_LIST,
					"U" AS STATUS,
					"system" AS USER_FROM
				FROM 
				(
					SELECT 
						MRD_ID,
						MRTS_ID,
						CUSTOMER_NAME,
						SEAT_LIST,
						STATUS,
						ROW_NUMBER() OVER (PARTITION BY SEAT_LIST,MRTS_ID ORDER BY MAINTAIN_DATE DESC) AS COL_TOP
					FROM MR_TRANSACTION_HISTORY WHERE MRTS_ID IN (
						SELECT MRTS_ID FROM 
						(	SELECT 
							MRTS_ID, START_TIME, END_TIME,
							(SELECT CONVERT(DATETIME,CONCAT(B.START_DATE," ",A.START_TIME)) FROM MR_DETAILS B WHERE MRD_ID = A.MRD_ID) AS START_DATE_TIME
							FROM MR_TIME_SLOTS A
							WHERE
							MRD_ID IN (SELECT MRD_ID FROM MR_DETAILS WHERE START_DATE <= CONVERT(DATETIME, GETDATE()))
						) a
						WHERE START_DATE_TIME <= CONVERT(DATETIME, GETDATE())
					) 
				) AS SEAT_TAKEN
				WHERE COL_TOP = 1 AND STATUS = "R"', 
    @database_name=@database_name, 
    @flags=0;

-- Update job to set start step:
EXEC msdb.dbo.sp_update_job
    @job_name=@job_name, 
    @enabled=1, 
    @start_step_id=1, 
    @notify_level_eventlog=0, 
    @notify_level_email=2, 
    @notify_level_netsend=2, 
    @notify_level_page=2, 
    @delete_level=0, 
    @description=@description, 
    @category_name=N'[Uncategorized (Local)]', 
    @owner_login_name=@owner_login_name, 
    @notify_email_operator_name=N'', 
    @notify_netsend_operator_name=N'', 
    @notify_page_operator_name=N'';

-- Schedule job:
EXEC msdb.dbo.sp_add_jobschedule
    @job_name=@job_name,
    @name=N'Update Reserved to Used',
    @enabled=1,
    @freq_type=4, -- Daily
    @freq_interval=1, -- recurring daily
    @freq_subday_type=4, -- Minutes - Interval Type
    @freq_subday_interval=30, -- 30 Minutes Interval
    -- @freq_relative_interval=0, freq_type is not monthly (32) 
    -- @freq_recurrence_factor=1, freq_type not in 8, 16, 32
    @active_start_date=null, -- default value is null, therefore, today's date
    @active_end_date=99991231, --YYYYMMDD (this represents no end date)
    @active_start_time=000000, --HHMMSS
    @active_end_time=235959; --HHMMSS
