<?php

namespace App\Projects;

use App\Records\ProjectSummary;
use App\Records\RecordStore;
use App\Support\TextComparison;
use Throwable;

final class ProjectManager
{
    public function __construct(
        private RecordStore $records,
        private TextComparison $textComparison,
    ) {}

    public function list(): ProjectListResult
    {
        try {
            $projects = $this->records->allProjects();
        } catch (Throwable) {
            return ProjectListResult::failure('RETRIEVAL', 'Projects could not be loaded.');
        }

        usort(
            $projects,
            fn (ProjectSummary $left, ProjectSummary $right): int => $this->textComparison->compare($left->name, $right->name),
        );

        return ProjectListResult::success($projects);
    }

    public function create(string $name): ProjectOperationResult
    {
        if (trim($name) === '') {
            return ProjectOperationResult::failure('ERR-004', 'Project Name is required.');
        }

        try {
            $this->records->createProject($name);
        } catch (Throwable) {
            return ProjectOperationResult::failure('ERR-007', 'The Project could not be saved.');
        }

        return ProjectOperationResult::success();
    }

    public function rename(int $id, string $name): ProjectOperationResult
    {
        if (trim($name) === '') {
            return ProjectOperationResult::failure('ERR-004', 'Project Name is required.');
        }

        try {
            $this->records->renameProject($id, $name);
        } catch (ProjectIsFixed) {
            return ProjectOperationResult::failure('ERR-007', 'The Project could not be saved.');
        } catch (Throwable) {
            return ProjectOperationResult::failure('ERR-007', 'The Project could not be saved.');
        }

        return ProjectOperationResult::success();
    }

    public function delete(int $id): ProjectOperationResult
    {
        try {
            $this->records->deleteProject($id);
        } catch (ProjectHasItems) {
            return ProjectOperationResult::failure('ERR-005', 'The Project has Items.');
        } catch (ProjectIsFixed) {
            return ProjectOperationResult::failure('ERR-007', 'The Project could not be saved.');
        } catch (Throwable) {
            return ProjectOperationResult::failure('ERR-007', 'The Project could not be saved.');
        }

        return ProjectOperationResult::success();
    }

    public function archive(int $id): ProjectOperationResult
    {
        try {
            $this->records->setProjectArchived($id, true);
        } catch (ProjectIsFixed) {
            return ProjectOperationResult::failure('ERR-007', 'The Project could not be saved.');
        } catch (Throwable) {
            return ProjectOperationResult::failure('ERR-007', 'The Project could not be saved.');
        }

        return ProjectOperationResult::success();
    }

    public function restore(int $id): ProjectOperationResult
    {
        try {
            $this->records->setProjectArchived($id, false);
        } catch (ProjectIsFixed) {
            return ProjectOperationResult::failure('ERR-007', 'The Project could not be saved.');
        } catch (Throwable) {
            return ProjectOperationResult::failure('ERR-007', 'The Project could not be saved.');
        }

        return ProjectOperationResult::success();
    }
}
