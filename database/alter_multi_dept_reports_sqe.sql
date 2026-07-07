-- Add missing SQE columns to multi_dept_reports
-- Run this SQL in phpMyAdmin once

ALTER TABLE `multi_dept_reports`
    ADD COLUMN `highlight_from` TEXT COLLATE utf8mb4_general_ci NULL AFTER `remark`,
    ADD COLUMN `customer` TEXT COLLATE utf8mb4_general_ci NULL AFTER `highlight_from`,
    ADD COLUMN `product_number` TEXT COLLATE utf8mb4_general_ci NULL AFTER `customer`,
    ADD COLUMN `supplier` TEXT COLLATE utf8mb4_general_ci NULL AFTER `product_number`,
    ADD COLUMN `issue` TEXT COLLATE utf8mb4_general_ci NULL AFTER `supplier`,
    ADD COLUMN `issue_description` TEXT COLLATE utf8mb4_general_ci NULL AFTER `issue`,
    ADD COLUMN `stock` TEXT COLLATE utf8mb4_general_ci NULL AFTER `issue_description`,
    ADD COLUMN `immediately_action` TEXT COLLATE utf8mb4_general_ci NULL AFTER `stock`,
    ADD COLUMN `sorting_rework` TEXT COLLATE utf8mb4_general_ci NULL AFTER `immediately_action`,
    ADD COLUMN `8d_report_received_day` DATE NULL AFTER `sorting_rework`,
    ADD COLUMN `action_lot` TEXT COLLATE utf8mb4_general_ci NULL AFTER `8d_report_received_day`,
    ADD COLUMN `sqe_owner` TEXT COLLATE utf8mb4_general_ci NULL AFTER `action_lot`,
    ADD COLUMN `btc_no` TEXT COLLATE utf8mb4_general_ci NULL AFTER `sqe_owner`;
