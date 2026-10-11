  <!-- this form is for add new extinguisher -->
            <div
              class="modal fade"
              id="addFireExtinguisherModal"
              tabindex="-1"
              aria-labelledby="addFireExtinguisherModalLabel"
              aria-hidden="true">
              <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">

                <div class="modal-content rounded-4 border-0 shadow">

                  <!-- Header -->
                  <div class="modal-header px-4 py-3">
                    <div>
                      <h5 class="modal-title fw-semibold mb-1" id="addFireExtinguisherModalLabel">
                        Add Fire Extinguisher
                      </h5>

                      <small class="text-body-secondary">
                        Register a new fire extinguisher
                      </small>
                    </div>

                    <button
                      type="button"
                      class="btn-close"
                      data-bs-dismiss="modal"
                      aria-label="Close"></button>
                  </div>


                  <!-- Body -->
                  <div class="modal-body px-4 py-4">

                    <form
                      class="row g-3"
                      action="backend/controller/FireExtinguisherController.php"
                      method="post">

                      <input
                        type="hidden"
                        name="action"
                        value="add-new-extinguisher">


                      <!-- Fire Extinguisher Code -->
                      <div class="col-md-6">
                        <label for="extinguisherCode" class="form-label">
                          Fire Extinguisher Code
                        </label>

                        <input
                          type="text"
                          class="form-control"
                          id="extinguisherCode"
                          name="extinguisher_code"
                          placeholder="e.g. FE-001"
                          required>

                        <div id="extinguisherCodeFeedback" class="small mt-1"></div>
                      </div>


                      <!-- Type -->
                      <div class="col-md-6">
                        <label for="type" class="form-label">
                          Type
                        </label>

                        <select
                          id="type"
                          name="type"
                          class="form-select"
                          required>
                          <option value="" selected disabled>
                            Select type
                          </option>

                          <?php
                          $types = getAllTypeDropdownController();

                          if (!empty($types)):
                            foreach ($types as $type):
                          ?>
                              <option value="<?= htmlspecialchars($type['value']) ?>">
                                <?= htmlspecialchars($type['value']) ?>
                              </option>
                          <?php
                            endforeach;
                          endif;
                          ?>
                        </select>
                      </div>


                      <!-- Capacity -->
                      <div class="col-md-4">
                        <label for="capacity" class="form-label">
                          Capacity
                        </label>

                        <select
                          class="form-select"
                          id="capacity"
                          name="capacity"
                          required>
                          <option value="" selected disabled>
                            Select capacity
                          </option>

                          <?php
                          $capacities = getAllCapacityDropdownController();

                          if (!empty($capacities)):
                            foreach ($capacities as $capacity):
                          ?>
                              <option value="<?= htmlspecialchars($capacity['value']) ?>">
                                <?= htmlspecialchars($capacity['value']) ?>
                              </option>
                          <?php
                            endforeach;
                          endif;
                          ?>
                        </select>
                      </div>


                      <!-- Class -->
                      <div class="col-md-4">
                        <label for="fireClass" class="form-label">
                          Fire Class
                        </label>

                        <select
                          id="fireClass"
                          name="class"
                          class="form-select"
                          required>
                          <option value="" selected disabled>
                            Select class
                          </option>
                          <option value="AB">AB</option>
                          <option value="ABC">ABC</option>
                          <option value="BC">BC</option>
                          <option value="A">A</option>
                          <option value="B">B</option>
                          <option value="C">C</option>
                          <option value="D">D</option>

                        </select>
                      </div>


                      <!-- Placement -->
                      <div class="col-md-4">
                        <label for="placement" class="form-label">
                          Placement
                        </label>

                        <select
                          id="placement"
                          name="placement"
                          class="form-select"
                          required>
                          <option value="" selected disabled>
                            Select placement
                          </option>

                          <?php
                          $placements = getAllPlacementDropdownController();

                          if (!empty($placements)):
                            foreach ($placements as $placement):
                          ?>
                              <option value="<?= htmlspecialchars($placement['value']) ?>">
                                <?= htmlspecialchars($placement['value']) ?>
                              </option>
                          <?php
                            endforeach;
                          endif;
                          ?>
                        </select>
                      </div>

                      <!-- Location -->
                      <div class="col-12 col-md-8">
                        <label for="location" class="form-label">
                          Location
                        </label>

                        <input
                          type="text"
                          class="form-control"
                          id="location"
                          name="location"
                          placeholder="e.g. Building 1 - 2nd Floor"
                          required>
                      </div>

                      <!-- Condition -->
                      <div class="col-12 col-md-4">
                        <label for="conditionStatus" class="form-label">
                          Condition
                        </label>

                        <select
                          id="conditionStatus"
                          name="condition_status"
                          class="form-select"
                          required>

                          <option value="" selected disabled>
                            Select condition
                          </option>

                          <option value="Good">Good</option>
                          <option value="Not Good">Not Good</option>

                        </select>
                      </div>


                      <!-- Manufactured Date -->
                      <div class="col-md-6">
                        <label for="manufacturedDate" class="form-label">
                          Manufactured Date
                        </label>

                        <input
                          type="date"
                          class="form-control"
                          id="manufacturedDate"
                          name="manufactured_date">
                      </div>


                      <!-- Expiration Date -->
                      <div class="col-md-6">
                        <label for="expirationDate" class="form-label">
                          Expiration Date
                        </label>

                        <input
                          type="date"
                          class="form-control"
                          id="expirationDate"
                          name="expiration_date"
                          required
                          readonly>
                      </div>

                      <!-- Branch -->
                      <div class="col-12 col-md-4">
                        <label for="branch" class="form-label">
                          Branch
                        </label>

                        <select
                          id="branch"
                          name="branch"
                          class="form-select"
                          required
                          onchange="generateFireExtinguisherCode()">
                          <option value="" selected disabled>
                            Select branch
                          </option>

                          <?php foreach ($branchDropdown as $branch): ?>
                            <option value="<?= htmlspecialchars($branch['value']) ?>">
                              <?= htmlspecialchars($branch['value']) ?>
                            </option>
                          <?php endforeach; ?>

                        </select>
                      </div>

                      <div class="col-12 text-end">
                        <small class="text-body-secondary">
                          Automatically computed as 3 years from the manufactured date.
                        </small>
                      </div>


                      <!-- Remarks -->
                      <div class="col-12">
                        <label for="remarks" class="form-label">
                          Remarks
                        </label>

                        <textarea
                          class="form-control"
                          id="remarks"
                          name="remarks"
                          rows="2"
                          placeholder="Additional remarks (optional)"></textarea>
                      </div>

                      <!-- Footer -->
                      <div class="modal-footer px-4 py-3">

                        <button
                          type="button"
                          class="btn btn-light border"
                          data-bs-dismiss="modal">
                          Cancel
                        </button>

                        <button
                          type="submit"
                          id="addFireExtinguisherBtn"
                          class="btn btn-primary px-4">
                          <i class="bi bi-plus-lg me-1"></i>
                          Add Fire Extinguisher
                        </button>

                      </div>

                    </form>

                  </div>

                </div>
              </div>
            </div>
